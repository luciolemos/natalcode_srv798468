<?php

declare(strict_types=1);

namespace Tests\Application\Middleware;

use App\Application\Middleware\EnvironmentAccessGuardMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\TestCase;

class EnvironmentAccessGuardMiddlewareTest extends TestCase
{
    private const MANAGED_ENV_KEYS = ['APP_ENV', 'APP_ACCESS_GUARD_USER', 'APP_ACCESS_GUARD_PASS'];

    /** @var array<string, string|null> */
    private array $originalEnv = [];

    protected function setUp(): void
    {
        parent::setUp();

        foreach (self::MANAGED_ENV_KEYS as $key) {
            $this->originalEnv[$key] = array_key_exists($key, $_ENV) ? (string) $_ENV[$key] : null;
            unset($_ENV[$key]);
        }
    }

    protected function tearDown(): void
    {
        foreach (self::MANAGED_ENV_KEYS as $key) {
            if ($this->originalEnv[$key] === null) {
                unset($_ENV[$key]);
                continue;
            }

            $_ENV[$key] = $this->originalEnv[$key];
        }

        parent::tearDown();
    }

    public function testProductionWithoutGuardPassesThroughWithoutNoindex(): void
    {
        $_ENV['APP_ENV'] = 'production';

        $response = $this->dispatchThroughGuard('/');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertFalse($response->hasHeader('X-Robots-Tag'));
    }

    public function testNonProductionAddsNoindexHeader(): void
    {
        $_ENV['APP_ENV'] = 'development';

        $response = $this->dispatchThroughGuard('/');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('noindex', $response->getHeaderLine('X-Robots-Tag'));
    }

    public function testGuardRejectsMissingAndWrongCredentials(): void
    {
        $this->enableGuard();

        $missing = $this->dispatchThroughGuard('/');
        $this->assertSame(401, $missing->getStatusCode());
        $this->assertStringStartsWith('Basic ', $missing->getHeaderLine('WWW-Authenticate'));

        $wrong = $this->dispatchThroughGuard('/', 'Basic ' . base64_encode('dev:errada'));
        $this->assertSame(401, $wrong->getStatusCode());
    }

    public function testGuardAcceptsValidCredentials(): void
    {
        $this->enableGuard();

        $response = $this->dispatchThroughGuard('/painel', 'Basic ' . base64_encode('dev:s3nha:com:dois-pontos'));

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGuardExemptsHealthEndpoints(): void
    {
        $this->enableGuard();

        $this->assertSame(200, $this->dispatchThroughGuard('/health')->getStatusCode());
        $this->assertSame(200, $this->dispatchThroughGuard('/health/db')->getStatusCode());
    }

    private function enableGuard(): void
    {
        $_ENV['APP_ENV'] = 'development';
        $_ENV['APP_ACCESS_GUARD_USER'] = 'dev';
        $_ENV['APP_ACCESS_GUARD_PASS'] = 's3nha:com:dois-pontos';
    }

    private function dispatchThroughGuard(string $path, string $authorization = ''): ResponseInterface
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', 'https://example.test' . $path);

        if ($authorization !== '') {
            $request = $request->withHeader('Authorization', $authorization);
        }

        $middleware = new EnvironmentAccessGuardMiddleware(new ResponseFactory());

        return $middleware->process($request, new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return (new ResponseFactory())->createResponse(200);
            }
        });
    }
}
