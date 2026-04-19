<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FaqParticipationPageAction extends AbstractPageAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        $homeContent = require __DIR__ . '/../../../../app/content/home.php';
        $faqItems = array_values(array_filter(
            (array) ($homeContent['faqItems'] ?? []),
            static fn (mixed $item): bool => is_array($item) && (string) ($item['category'] ?? '') === 'participacao'
        ));

        $structuredData = [];
        $faqSchema = $this->buildFaqStructuredData($faqItems, 'https://natalcode.com.br/faq/participacao');
        if ($faqSchema !== null) {
            $structuredData[] = $faqSchema;
        }

        return $this->renderPage($response, 'pages/faq-category.twig', [
            'faq_category_slug' => 'participacao',
            'page_title' => 'FAQ Participação | NatalCode',
            'page_url' => 'https://natalcode.com.br/faq/participacao',
            'page_description' => 'Perguntas frequentes sobre participação e integração nas atividades do NatalCode.',
            'structured_data' => $structuredData,
        ]);
    }
}
