<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FaqPracticesPageAction extends AbstractPageAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        return $this->renderPage($response, 'pages/faq-category.twig', [
            'faq_category_slug' => 'praticas',
            'page_title' => 'FAQ Entrega | NatalCode',
            'page_url' => 'https://natalcode.com.br/faq/praticas',
            'page_description' => 'Perguntas frequentes sobre suporte, manutencao e rotina de entrega.',
        ]);
    }
}
