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
                $document['title'] = trim((string) ($storedContent['title'] ?? $document['title']));
                $document['body'] = str_replace(
                    ["\r\n", "\r"],
                    "\n",
                    (string) ($storedContent['body'] ?? $document['body'])
                );

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
            'page_title' => 'Termos de Uso | CEDE',
            'page_url' => 'https://cedern.org/termos-de-uso',
            'page_description' => 'Termos de Uso do site institucional do CEDE.',
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
            'title' => trim((string) ($defaults['title'] ?? 'Termos de Uso')),
            'lead' => 'Regras de uso do site institucional do CEDE e das áreas restritas.',
            'body' => str_replace(["\r\n", "\r"], "\n", (string) ($defaults['body'] ?? '')),
            'updated_at_label' => '',
        ];
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
