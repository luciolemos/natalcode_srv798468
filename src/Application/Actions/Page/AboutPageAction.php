<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AboutPageAction extends AbstractPageAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        return $this->renderPage($response, 'pages/about.twig', [
            'page_title' => 'Quem Somos | NatalCode',
            'page_url' => 'https://natalcode.com.br/quem-somos',
            'page_description' => 'Conheca a NatalCode, sua missao, valores e frentes de atuacao em estrategia, design e desenvolvimento web.',
        ]);
    }
}
