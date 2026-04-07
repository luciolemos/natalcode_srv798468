<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StoreBazaarPageAction extends AbstractPageAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        $response = $this->renderPage($response, 'pages/store-bazaar.twig', [
            'page_title' => 'Bazar | Loja | NatalCode',
            'page_url' => 'https://natalcode.com.br/loja/bazar',
            'page_description' => 'O Bazar do NatalCode está em preparação e ainda não foi disponibilizado no site.',
        ]);

        return $response->withStatus(404);
    }
}
