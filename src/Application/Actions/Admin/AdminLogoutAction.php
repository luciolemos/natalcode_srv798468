<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\Page\AbstractPageAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminLogoutAction extends AbstractPageAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            session_start();
        }

        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE && ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            $sessionCookieName = session_name();

            if (is_string($sessionCookieName) && $sessionCookieName !== '') {
                setcookie(
                    $sessionCookieName,
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }
        }

        session_destroy();

        setcookie('dashboard_auth', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => $this->isHttpsRequest($request),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        return $response->withHeader('Location', '/painel/login')->withStatus(302);
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
