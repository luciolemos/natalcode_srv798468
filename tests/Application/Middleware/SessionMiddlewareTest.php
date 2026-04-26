<?php

declare(strict_types=1);

namespace Tests\Application\Middleware;

use App\Application\Middleware\SessionMiddleware;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Psr7\Uri;
use Tests\TestCase;

class SessionMiddlewareTest extends TestCase
{
    /** @var array<string, string> */
    private array $iniSnapshot = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->iniSnapshot = [
            'session.cookie_lifetime' => (string) ini_get('session.cookie_lifetime'),
            'session.cookie_path' => (string) ini_get('session.cookie_path'),
            'session.cookie_domain' => (string) ini_get('session.cookie_domain'),
            'session.cookie_secure' => (string) ini_get('session.cookie_secure'),
            'session.cookie_samesite' => (string) ini_get('session.cookie_samesite'),
            'session.use_only_cookies' => (string) ini_get('session.use_only_cookies'),
            'session.use_strict_mode' => (string) ini_get('session.use_strict_mode'),
            'session.cookie_httponly' => (string) ini_get('session.cookie_httponly'),
        ];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_write_close();
        }

        $_SESSION = [];
        $_SERVER['HTTPS'] = '';
    }

    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_write_close();
        }

        foreach ($this->iniSnapshot as $key => $value) {
            ini_set($key, $value);
        }

        unset($_SERVER['HTTPS']);
        $_SESSION = [];

        parent::tearDown();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testProcessStartsSessionAndAddsSessionAttribute(): void
    {
        $middleware = new SessionMiddleware();
        $request = $this->createRequestWithScheme('http');

        $capturedSession = null;
        $handler = new class ($capturedSession) implements RequestHandlerInterface {
            /** @var array<string, mixed>|null */
            public ?array $capturedSessionAttribute = null;

            public function __construct(?array &$capturedSession)
            {
                $this->capturedSessionAttribute = &$capturedSession;
            }

            public function handle(\Psr\Http\Message\ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface
            {
                $this->capturedSessionAttribute = $request->getAttribute('session');

                return new Response(200);
            }
        };

        $response = $middleware->process($request, $handler);
        $cookieParams = session_get_cookie_params();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertIsArray($capturedSession);
        $this->assertFalse((bool) ($cookieParams['secure'] ?? true));
        $this->assertSame('Lax', (string) ($cookieParams['samesite'] ?? ''));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testProcessMarksCookieAsSecureWhenRequestIsHttps(): void
    {
        $middleware = new SessionMiddleware();
        $request = $this->createRequestWithScheme('https');

        $handler = new class () implements RequestHandlerInterface {
            public function handle(\Psr\Http\Message\ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface
            {
                return new Response(204);
            }
        };

        $response = $middleware->process($request, $handler);
        $cookieParams = session_get_cookie_params();

        $this->assertSame(204, $response->getStatusCode());
        $this->assertTrue((bool) ($cookieParams['secure'] ?? false));
    }

    private function createRequestWithScheme(string $scheme): Request
    {
        $uri = new Uri($scheme, 'localhost', $scheme === 'https' ? 443 : 80, '/teste');

        return new Request('GET', $uri, new Headers(), [], [], (new StreamFactory())->createStream(''));
    }
}
