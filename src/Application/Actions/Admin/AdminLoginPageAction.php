<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\Page\AbstractPageAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminLoginPageAction extends AbstractPageAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        if (!empty($_SESSION['admin_authenticated'])) {
            return $response->withHeader('Location', '/painel')->withStatus(302);
        }

        if (!empty($_SESSION['member_authenticated'])) {
            return $response->withHeader('Location', '/painel')->withStatus(302);
        }

        return $response->withHeader('Location', '/entrar')->withStatus(302);
    }
}
