<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StorePageAction extends AbstractPageAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        return $this->renderPage($response, 'pages/store.twig', [
            'page_title' => 'Loja | NatalCode',
            'page_url' => 'https://natalcode.com.br/loja',
            'page_description' =>
                'Acesse a área de loja do NatalCode '
                . 'e consulte o catalogo da NatalCode Labs com atendimento presencial.',
        ]);
    }
}
