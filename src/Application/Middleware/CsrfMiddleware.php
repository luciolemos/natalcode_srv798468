<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class CsrfMiddleware implements Middleware
{
    private const SESSION_TOKEN_KEY = '_csrf_token';

    /**
     * @var array<int, string>
     */
    private const EXCLUDED_PATH_PREFIXES = [
        '/events',
    ];

    public function process(Request $request, RequestHandler $handler): Response
    {
        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            session_start();
        }

        $token = $this->ensureSessionToken();

        if (!$this->isStateChangingMethod($request->getMethod())) {
            return $handler->handle($request);
        }

        if ($this->isExcludedPath($request->getUri()->getPath())) {
            return $handler->handle($request);
        }

        if ($this->hasValidToken($request, $token)) {
            return $handler->handle($request);
        }

        $response = new SlimResponse(403);
        $response->getBody()->write('CSRF token invalido ou ausente. Atualize a pagina e tente novamente.');

        return $response->withHeader('Content-Type', 'text/plain; charset=UTF-8');
    }

    private function ensureSessionToken(): string
    {
        $currentToken = trim((string) ($_SESSION[self::SESSION_TOKEN_KEY] ?? ''));

        if (preg_match('/^[a-f0-9]{64}$/', $currentToken) === 1) {
            return $currentToken;
        }

        $newToken = $this->generateToken();
        $_SESSION[self::SESSION_TOKEN_KEY] = $newToken;

        return $newToken;
    }

    private function generateToken(): string
    {
        try {
            return bin2hex(random_bytes(32));
        } catch (\Throwable $exception) {
            return hash('sha256', uniqid('csrf', true));
        }
    }

    private function isStateChangingMethod(string $method): bool
    {
        return in_array(strtoupper(trim($method)), ['POST', 'PUT', 'PATCH', 'DELETE'], true);
    }

    private function isExcludedPath(string $path): bool
    {
        $normalizedPath = '/' . ltrim(trim($path), '/');

        foreach (self::EXCLUDED_PATH_PREFIXES as $prefix) {
            $normalizedPrefix = '/' . ltrim(trim($prefix), '/');

            if ($normalizedPath === $normalizedPrefix || str_starts_with($normalizedPath, $normalizedPrefix . '/')) {
                return true;
            }
        }

        return false;
    }

    private function hasValidToken(Request $request, string $sessionToken): bool
    {
        $headerToken = trim((string) $request->getHeaderLine('X-CSRF-Token'));
        if ($headerToken !== '' && hash_equals($sessionToken, $headerToken)) {
            return true;
        }

        $parsedBody = $request->getParsedBody();
        if (is_array($parsedBody)) {
            $bodyToken = trim((string) ($parsedBody['_csrf'] ?? ''));
            if ($bodyToken !== '' && hash_equals($sessionToken, $bodyToken)) {
                return true;
            }
        }

        return false;
    }
}
