<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FaqPageAction extends AbstractPageAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        return $this->renderPage($response, 'pages/faq.twig', [
            'page_title' => 'FAQ | NatalCode',
            'page_url' => 'https://natalcode.com.br/faq',
            'page_description' => 'Duvidas frequentes sobre servicos e processos da NatalCode.',
        ]);
    }
}
