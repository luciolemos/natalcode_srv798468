<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class SessionMiddleware implements Middleware
{
    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            $this->configureSessionCookieParams($request);
            session_start();
        }

        $request = $request->withAttribute('session', $_SESSION ?? []);

        return $handler->handle($request);
    }

    private function configureSessionCookieParams(Request $request): void
    {
        $isSecureRequest = $this->isHttpsRequest($request);

        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Lax');
        ini_set('session.cookie_secure', $isSecureRequest ? '1' : '0');

        $lifetime = (int) ini_get('session.cookie_lifetime');
        $path = (string) ini_get('session.cookie_path');
        $domain = (string) ini_get('session.cookie_domain');

        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path' => $path !== '' ? $path : '/',
            'domain' => $domain,
            'secure' => $isSecureRequest,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    private function isHttpsRequest(Request $request): bool
    {
        $uriScheme = strtolower(trim($request->getUri()->getScheme()));
        $forwardedProto = strtolower(trim((string) $request->getHeaderLine('X-Forwarded-Proto')));
        $serverHttps = strtolower((string) ($_SERVER['HTTPS'] ?? ''));

        return $uriScheme === 'https'
            || $forwardedProto === 'https'
            || ($serverHttps !== '' && $serverHttps !== 'off');
    }
}
