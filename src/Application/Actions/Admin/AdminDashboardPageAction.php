<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\Page\AbstractPageAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminDashboardPageAction extends AbstractPageAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        return $this->renderPage($response, 'pages/admin-dashboard-home.twig', [
            'page_title' => 'Dashboard Admin | CEDE',
            'page_url' => 'https://cedern.org/painel',
            'page_description' => 'Painel administrativo da agenda.',
        ]);
    }
}
