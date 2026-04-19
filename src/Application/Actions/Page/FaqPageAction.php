<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FaqPageAction extends AbstractPageAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        $homeContent = require __DIR__ . '/../../../../app/content/home.php';
        $faqItems = (array) ($homeContent['faqItems'] ?? []);

        $structuredData = [];
        $faqSchema = $this->buildFaqStructuredData($faqItems, 'https://natalcode.com.br/faq');
        if ($faqSchema !== null) {
            $structuredData[] = $faqSchema;
        }

        return $this->renderPage($response, 'pages/faq.twig', [
            'page_title' => 'FAQ | NatalCode',
            'page_url' => 'https://natalcode.com.br/faq',
            'page_description' => 'Duvidas frequentes sobre servicos e processos da NatalCode.',
            'structured_data' => $structuredData,
        ]);
    }
}
