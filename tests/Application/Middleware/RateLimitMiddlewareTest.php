<?php

declare(strict_types=1);

namespace Tests\Application\Middleware;

use App\Application\Middleware\RateLimitMiddleware;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Tests\TestCase;

final class RateLimitMiddlewareTest extends TestCase
{
    /** @var array<string, string|null> */
    private array $originalEnv = [];

    private string $storageDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->backupEnv();
        $this->storageDir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
            . '/natalcode-rate-limit-test-'
            . uniqid('', true);
        $_ENV['APP_RATE_LIMIT_STORAGE_DIR'] = $this->storageDir;
    }

    protected function tearDown(): void
    {
        $this->removeDirectoryRecursively($this->storageDir);
        $this->restoreEnv();

        parent::tearDown();
    }

    public function testProcessSkipsRateLimitWhenDisabledInTestEnvironment(): void
    {
        $_ENV['APP_ENV'] = 'test';
        unset($_ENV['APP_RATE_LIMIT_ENABLED']);

        $middleware = new RateLimitMiddleware();
        $handled = 0;
        $handler = $this->buildHandler($handled, 204);

        $request = $this->createRequest('POST', '/entrar', ['HTTP_ACCEPT' => 'application/json'], [], [
            'REMOTE_ADDR' => '198.51.100.20',
        ])->withParsedBody([
            'identifier' => 'contato@natalcode.com.br',
        ]);

        $response = $middleware->process($request, $handler);

        $this->assertSame(204, $response->getStatusCode());
        $this->assertSame(1, $handled);
    }

    public function testEntrarRuleReturns429AfterIdentityLimitIsExceeded(): void
    {
        $_ENV['APP_ENV'] = 'test';
        $_ENV['APP_RATE_LIMIT_ENABLED'] = 'true';

        $middleware = new RateLimitMiddleware();
        $handled = 0;
        $handler = $this->buildHandler($handled, 204);
        $request = $this->createRequest('POST', '/entrar', ['HTTP_ACCEPT' => 'application/json'], [], [
            'REMOTE_ADDR' => '198.51.100.30',
        ])->withParsedBody([
            'identifier' => 'mesmo-usuario@natalcode.com.br',
        ]);

        for ($attempt = 1; $attempt <= 8; $attempt++) {
            $response = $middleware->process($request, $handler);
            $this->assertSame(204, $response->getStatusCode(), 'Tentativa permitida #' . $attempt);
        }

        $limitedResponse = $middleware->process($request, $handler);

        $this->assertSame(429, $limitedResponse->getStatusCode());
        $this->assertSame('application/json', $limitedResponse->getHeaderLine('Content-Type'));
        $this->assertNotSame('', $limitedResponse->getHeaderLine('Retry-After'));

        $payload = json_decode((string) $limitedResponse->getBody(), true);
        $this->assertIsArray($payload);
        $this->assertSame('rate_limited', $payload['status'] ?? null);
        $this->assertSame('member_login_identity', $payload['rule'] ?? null);
        $this->assertSame(8, $handled);
    }

    public function testEntrarIdentityAllowanceIsIsolatedPerIdentifier(): void
    {
        $_ENV['APP_ENV'] = 'test';
        $_ENV['APP_RATE_LIMIT_ENABLED'] = 'true';

        $middleware = new RateLimitMiddleware();
        $handled = 0;
        $handler = $this->buildHandler($handled, 204);

        $baseHeaders = ['HTTP_ACCEPT' => 'application/json'];
        $serverParams = ['REMOTE_ADDR' => '198.51.100.40'];

        $firstIdentityRequest = $this->createRequest('POST', '/entrar', $baseHeaders, [], $serverParams)
            ->withParsedBody(['identifier' => 'primeiro@natalcode.com.br']);

        for ($attempt = 1; $attempt <= 8; $attempt++) {
            $response = $middleware->process($firstIdentityRequest, $handler);
            $this->assertSame(204, $response->getStatusCode());
        }

        $secondIdentityRequest = $this->createRequest('POST', '/entrar', $baseHeaders, [], $serverParams)
            ->withParsedBody(['identifier' => 'segundo@natalcode.com.br']);
        $secondIdentityResponse = $middleware->process($secondIdentityRequest, $handler);
        $this->assertSame(204, $secondIdentityResponse->getStatusCode());

        $firstIdentityBlockedResponse = $middleware->process($firstIdentityRequest, $handler);
        $this->assertSame(429, $firstIdentityBlockedResponse->getStatusCode());

        $payload = json_decode((string) $firstIdentityBlockedResponse->getBody(), true);
        $this->assertIsArray($payload);
        $this->assertSame('member_login_identity', $payload['rule'] ?? null);
        $this->assertSame(9, $handled);
    }

    private function backupEnv(): void
    {
        foreach ($this->managedEnvKeys() as $key) {
            $this->originalEnv[$key] = array_key_exists($key, $_ENV) ? (string) $_ENV[$key] : null;
        }
    }

    private function restoreEnv(): void
    {
        foreach ($this->managedEnvKeys() as $key) {
            $originalValue = $this->originalEnv[$key] ?? null;

            if ($originalValue === null) {
                unset($_ENV[$key]);
                continue;
            }

            $_ENV[$key] = $originalValue;
        }
    }

    /**
     * @return list<string>
     */
    private function managedEnvKeys(): array
    {
        return [
            'APP_ENV',
            'APP_RATE_LIMIT_ENABLED',
            'APP_RATE_LIMIT_STORAGE_DIR',
        ];
    }

    private function removeDirectoryRecursively(string $path): void
    {
        if ($path === '' || !is_dir($path)) {
            return;
        }

        $items = scandir($path);
        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $itemPath = $path . DIRECTORY_SEPARATOR . $item;
            if (is_dir($itemPath)) {
                $this->removeDirectoryRecursively($itemPath);
                continue;
            }

            @unlink($itemPath);
        }

        @rmdir($path);
    }

    private function buildHandler(int &$handled, int $statusCode): RequestHandlerInterface
    {
        return new class ($handled, $statusCode) implements RequestHandlerInterface {
            private int $statusCode;

            /** @var int */
            private $handled;

            public function __construct(int &$handled, int $statusCode)
            {
                $this->handled = &$handled;
                $this->statusCode = $statusCode;
            }

            public function handle(\Psr\Http\Message\ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface
            {
                $this->handled++;

                return new Response($this->statusCode);
            }
        };
    }
}
