<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AboutBrandPageAction extends AbstractPageAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        return $this->renderPage($response, 'pages/about-brand.twig', [
            'page_title' => 'Nossa Marca | NatalCode',
            'page_url' => 'https://natalcode.com.br/quem-somos/nossa-marca',
            'page_description' => 'Conheca a direcao visual da marca NatalCode e os principios de identidade aplicados nos canais digitais.',
        ]);
    }
}
