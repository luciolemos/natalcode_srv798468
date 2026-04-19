<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomePageAction extends AbstractPageAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        return $this->renderPage($response, 'home.twig', [
            'page_title' => 'NatalCode | Agencia Digital',
            'page_description' => 'Agencia digital especializada em sites, landing pages, SEO e sistemas web para '
                . 'negocios que precisam gerar mais contatos e vendas.',
            'page_url' => 'https://natalcode.com.br/',
        ]);
    }
}
