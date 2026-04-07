<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use App\Domain\Institutional\InstitutionalContentRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use Throwable;

class AboutStatutePageAction extends AbstractPageAction
{
    private const STATUTE_SLUG = 'estatuto';

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
        $statute = $this->loadDefaultStatute();

        try {
            $storedContent = $this->institutionalContentRepository->findBySlug(self::STATUTE_SLUG);

            if ($storedContent !== null && trim((string) ($storedContent['body'] ?? '')) !== '') {
                $statute['title'] = trim((string) ($storedContent['title'] ?? $statute['title']));
                $statute['body'] = str_replace(
                    ["\r\n", "\r"],
                    "\n",
                    (string) ($storedContent['body'] ?? $statute['body'])
                );

                $updatedAt = trim((string) ($storedContent['updated_at'] ?? ''));
                $statute['updated_at_label'] = $this->formatDateTimeLabel($updatedAt);
            }
        } catch (Throwable $exception) {
            $this->logger->warning('Falha ao carregar conteúdo institucional do estatuto.', [
                'error' => $exception->getMessage(),
            ]);
        }

        return $this->renderPage($response, 'pages/about-statute.twig', [
            'statute' => $statute,
            'page_title' => 'Estatuto | NatalCode',
            'page_url' => 'https://natalcode.com.br/quem-somos/estatuto',
            'page_description' => 'Consulte o Estatuto completo do NatalCode.',
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function loadDefaultStatute(): array
    {
        $defaultsPath = dirname(__DIR__, 4) . '/app/content/statute.php';
        $defaults = [];

        if (is_file($defaultsPath) && is_readable($defaultsPath)) {
            $loaded = require $defaultsPath;

            if (is_array($loaded)) {
                $defaults = $loaded;
            }
        }

        return [
            'kicker' => 'Quem Somos',
            'title' => trim((string) ($defaults['title'] ?? 'ESTATUTO')),
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
