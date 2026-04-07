<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FaqDoctrinePageAction extends AbstractPageAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        return $this->renderPage($response, 'pages/faq-category.twig', [
            'faq_category_slug' => 'doutrina',
            'page_title' => 'FAQ Estrategia | NatalCode',
            'page_url' => 'https://natalcode.com.br/faq/doutrina',
            'page_description' => 'Perguntas frequentes sobre estrategia digital e planejamento de projeto.',
        ]);
    }
}
