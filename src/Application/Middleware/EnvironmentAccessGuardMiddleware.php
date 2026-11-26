<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * Protege ambientes que não são de produção (ex.: servidor de desenvolvimento público).
 *
 * - Fora de produção (APP_ENV != production), toda resposta recebe X-Robots-Tag: noindex, nofollow,
 *   evitando que buscadores indexem o ambiente de dev como conteúdo duplicado.
 * - Com APP_ACCESS_GUARD_USER e APP_ACCESS_GUARD_PASS definidos no .env, exige HTTP Basic Auth
 *   em todas as rotas da aplicação (exceto /health*). Em produção, basta não definir essas variáveis.
 */
class EnvironmentAccessGuardMiddleware implements Middleware
{
    private const ROBOTS_HEADER_VALUE = 'noindex, nofollow, noarchive';

    private ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $isProduction = $this->isProduction();
        $guardUser = (string) ($_ENV['APP_ACCESS_GUARD_USER'] ?? '');
        $guardPass = (string) ($_ENV['APP_ACCESS_GUARD_PASS'] ?? '');
        $guardEnabled = $guardUser !== '' && $guardPass !== '';

        if ($guardEnabled && !$this->isExemptPath($request) && !$this->hasValidCredentials($request, $guardUser, $guardPass)) {
            $response = $this->responseFactory->createResponse(401)
                ->withHeader('WWW-Authenticate', 'Basic realm="NatalCode (ambiente restrito)", charset="UTF-8"')
                ->withHeader('Content-Type', 'text/plain; charset=utf-8')
                ->withHeader('Cache-Control', 'no-store')
                ->withHeader('X-Robots-Tag', self::ROBOTS_HEADER_VALUE);
            $response->getBody()->write('Acesso restrito.');

            return $response;
        }

        $response = $handler->handle($request);

        if (!$isProduction) {
            $response = $response->withHeader('X-Robots-Tag', self::ROBOTS_HEADER_VALUE);
        }

        return $response;
    }

    private function isProduction(): bool
    {
        $appEnv = strtolower(trim((string) ($_ENV['APP_ENV'] ?? 'production')));

        return in_array($appEnv, ['prod', 'production'], true);
    }

    private function isExemptPath(Request $request): bool
    {
        $path = $request->getUri()->getPath();

        return $path === '/health' || str_starts_with($path, '/health/');
    }

    private function hasValidCredentials(Request $request, string $expectedUser, string $expectedPass): bool
    {
        [$user, $pass] = $this->extractCredentials($request);

        if ($user === null || $pass === null) {
            return false;
        }

        // Compara os dois campos sempre, em tempo constante, para não vazar qual deles falhou.
        $userMatches = hash_equals($expectedUser, $user);
        $passMatches = hash_equals($expectedPass, $pass);

        return $userMatches && $passMatches;
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function extractCredentials(Request $request): array
    {
        $serverParams = $request->getServerParams();

        if (isset($serverParams['PHP_AUTH_USER'])) {
            return [(string) $serverParams['PHP_AUTH_USER'], (string) ($serverParams['PHP_AUTH_PW'] ?? '')];
        }

        $header = trim($request->getHeaderLine('Authorization'));

        if ($header === '' || stripos($header, 'basic ') !== 0) {
            return [null, null];
        }

        $decoded = base64_decode(trim(substr($header, 6)), true);

        if ($decoded === false || !str_contains($decoded, ':')) {
            return [null, null];
        }

        [$user, $pass] = explode(':', $decoded, 2);

        return [$user, $pass];
    }
}
