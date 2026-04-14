<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PortfolioPageAction extends AbstractPageAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        return $this->renderPage($response, 'pages/portfolio.twig', [
            'page_title' => 'Portfolio | NatalCode',
            'page_url' => 'https://natalcode.com.br/portfolio',
            'page_description' => 'Catalogo de projetos digitais da NatalCode: landing pages, sites institucionais e paginas de alta conversao.',
        ]);
    }
}
