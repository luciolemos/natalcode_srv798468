<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use App\Domain\Institutional\InstitutionalContentRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use Throwable;

class TermsOfUsePageAction extends AbstractPageAction
{
    private const DOCUMENT_SLUG = 'termos-de-uso';

    private InstitutionalContentRepository $institutionalContentRepository;

    public function __construct(
        LoggerInterface $logger,
        Twig $twig,
        InstitutionalContentRepository $institutionalContentRepository
    ) {
        parent::__construct($logger, $twig);
        $this->institutionalContentRepository = $institutionalContentRepository;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $document = $this->loadDefaultDocument();

        try {
            $storedContent = $this->institutionalContentRepository->findBySlug(self::DOCUMENT_SLUG);

            if ($storedContent !== null && trim((string) ($storedContent['body'] ?? '')) !== '') {
                $document['title'] = $this->normalizeLegacyReferences(
                    trim((string) ($storedContent['title'] ?? $document['title']))
                );
                $document['body'] = $this->normalizeLegacyReferences(str_replace(
                    ["\r\n", "\r"],
                    "\n",
                    (string) ($storedContent['body'] ?? $document['body'])
                ));

                $updatedAt = trim((string) ($storedContent['updated_at'] ?? ''));
                $document['updated_at_label'] = $this->formatDateTimeLabel($updatedAt);
            }
        } catch (Throwable $exception) {
            $this->logger->warning('Falha ao carregar termos de uso.', [
                'error' => $exception->getMessage(),
            ]);
        }

        return $this->renderPage($response, 'pages/legal-document.twig', [
            'legal_document' => $document,
            'page_title' => 'Termos de Uso | NatalCode',
            'page_url' => 'https://natalcode.com.br/termos-de-uso',
            'page_description' => 'Termos de Uso do site institucional do NatalCode.',
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function loadDefaultDocument(): array
    {
        $defaultsPath = dirname(__DIR__, 4) . '/app/content/terms-of-use.php';
        $defaults = [];

        if (is_file($defaultsPath) && is_readable($defaultsPath)) {
            $loaded = require $defaultsPath;

            if (is_array($loaded)) {
                $defaults = $loaded;
            }
        }

        return [
            'kicker' => 'Institucional',
            'title' => $this->normalizeLegacyReferences(trim((string) ($defaults['title'] ?? 'Termos de Uso'))),
            'lead' => 'Regras de uso do site institucional do NatalCode e das áreas restritas.',
            'body' => $this->normalizeLegacyReferences(str_replace(["\r\n", "\r"], "\n", (string) ($defaults['body'] ?? ''))),
            'updated_at_label' => '',
        ];
    }

    private function normalizeLegacyReferences(string $content): string
    {
        if ($content === '') {
            return $content;
        }

        $search = [
            'Centro de Estudos da Doutrina Espírita – NatalCode',
            'Centro de Estudos da Doutrina Espírita - NatalCode',
            'Centro de Estudos da Doutrina Espírita –',
            'Centro de Estudos da Doutrina Espírita -',
            'Centro de Estudos da Doutrina Espírita',
            'Centro Espírita de Doutrina Espírita',
            'Centro Espirita de Doutrina Espirita',
            'Centro Espírita',
            'Centro Espirita',
            'E-mail: cede@cedern.org',
            'cede@cedern.org',
            'CEDE',
            'Cede',
        ];
        $replace = [
            'NatalCode Agência Digital',
            'NatalCode Agência Digital',
            'NatalCode Agência Digital',
            'NatalCode Agência Digital',
            'NatalCode Agência Digital',
            'NatalCode Agência Digital',
            'NatalCode Agência Digital',
            'NatalCode Agência Digital',
            'NatalCode Agência Digital',
            'E-mail: contato@natalcode.com.br',
            'contato@natalcode.com.br',
            'NatalCode',
            'NatalCode',
        ];

        return str_replace($search, $replace, $content);
    }

    private function formatDateTimeLabel(string $value): string
    {
        if ($value === '') {
            return '';
        }

        try {
            $dateTime = new \DateTimeImmutable($value);

            return $dateTime->format('d/m/Y H:i');
        } catch (\Throwable $exception) {
            return '';
        }
    }
}
