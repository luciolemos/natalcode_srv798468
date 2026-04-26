<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class MemberLogoutAction extends AbstractPageAction
{
    public function __construct(LoggerInterface $logger, Twig $twig)
    {
        parent::__construct($logger, $twig);
    }

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
                setcookie($sessionCookieName, '', [
                    'expires' => time() - 42000,
                    'path' => (string) $params['path'],
                    'domain' => (string) $params['domain'],
                    'secure' => (bool) $params['secure'],
                    'httponly' => (bool) $params['httponly'],
                    'samesite' => 'Lax',
                ]);
            }
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        return $response->withHeader('Location', '/')->withStatus(302);
    }
}
