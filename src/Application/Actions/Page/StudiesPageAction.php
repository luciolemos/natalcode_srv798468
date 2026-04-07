<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StudiesPageAction extends AbstractPageAction
{
    public function __invoke(Request $request, Response $response): Response
    {

        return $this->renderPage($response, 'pages/studies.twig', [
            'page_title' => 'Estudos | NatalCode',
            'page_url' => 'https://natalcode.com.br/estudos',
            'page_description' => 'Conheca os servicos digitais e o modelo de entrega da NatalCode.',
        ]);
    }
}
