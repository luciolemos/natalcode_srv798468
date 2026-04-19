<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use App\Application\Security\RecaptchaVerifier;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

abstract class AbstractPageAction
{
    protected LoggerInterface $logger;

    protected Twig $twig;

    public function __construct(LoggerInterface $logger, Twig $twig)
    {
        $this->logger = $logger;
        $this->twig = $twig;
    }

    abstract public function __invoke(Request $request, Response $response): Response;

    /**
     * @param array<string, mixed> $data
     */
    protected function renderPage(Response $response, string $template, array $data = []): Response
    {
        $baseUrl = rtrim((string) ($_ENV['APP_DEFAULT_PAGE_URL'] ?? 'https://natalcode.com.br/'), '/');
        $defaultPageImage =
            'https://natalcode.com.br/assets/img/brand/natalcode1.png';
        $defaultPageDescription =
            'NatalCode Agencia Digital: criacao de sites, landing pages e sistemas web '
            . 'com foco em performance e conversao.';

        $context = array_merge([
            'homeContent' => require __DIR__ . '/../../../../app/content/home.php',
            'site_name' => trim((string) ($_ENV['APP_DEFAULT_SITE_NAME'] ?? 'NatalCode')),
            'page_image' => trim((string) ($_ENV['APP_DEFAULT_PAGE_IMAGE'] ?? $defaultPageImage)),
            'page_description' => trim((string) (
                $_ENV['APP_DEFAULT_PAGE_DESCRIPTION'] ?? $defaultPageDescription
            )),
            'page_url_base' => $baseUrl,
        ], $data);

        return $this->twig->render($response, $template, $context);
    }

    /**
     * @param array<int, array<string, mixed>> $faqItems
     * @return array<string, mixed>|null
     */
    protected function buildFaqStructuredData(array $faqItems, string $pageUrl): ?array
    {
        $mainEntity = [];

        foreach ($faqItems as $item) {
            $question = trim((string) ($item['question'] ?? ''));
            $answer = trim((string) ($item['answer'] ?? ''));

            if ($question === '' || $answer === '') {
                continue;
            }

            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $answer,
                ],
            ];
        }

        if ($mainEntity === []) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'url' => $pageUrl,
            'mainEntity' => $mainEntity,
        ];
    }

    /**
     * @param array<string, mixed> $event
     * @return array<string, mixed>|null
     */
    protected function buildEventStructuredData(array $event, string $pageUrl): ?array
    {
        $title = trim((string) ($event['title'] ?? ''));
        $startsAt = $this->normalizeDateTimeForSchema((string) ($event['starts_at'] ?? ''));

        if ($title === '' || $startsAt === null) {
            return null;
        }

        $description = trim((string) ($event['description'] ?? ''));
        if ($description === '') {
            $description = 'Detalhes da atividade na agenda da NatalCode.';
        }

        $endsAt = $this->normalizeDateTimeForSchema((string) ($event['ends_at'] ?? ''));
        $mode = strtolower(trim((string) ($event['mode'] ?? 'presencial')));
        $meetingUrl = trim((string) ($event['meeting_url'] ?? ''));
        $locationName = trim((string) ($event['location_name'] ?? 'NatalCode'));
        $locationAddress = trim((string) ($event['location_address'] ?? ''));
        $baseUrl = rtrim((string) ($_ENV['APP_DEFAULT_PAGE_URL'] ?? 'https://natalcode.com.br/'), '/');

        $attendanceMode = 'https://schema.org/OfflineEventAttendanceMode';
        if ($mode === 'online') {
            $attendanceMode = 'https://schema.org/OnlineEventAttendanceMode';
        } elseif ($mode === 'hibrido') {
            $attendanceMode = 'https://schema.org/MixedEventAttendanceMode';
        }

        $place = [
            '@type' => 'Place',
            'name' => $locationName,
        ];
        if ($locationAddress !== '') {
            $place['address'] = $locationAddress;
        }

        $location = $place;
        if ($mode === 'online' && $meetingUrl !== '') {
            $location = [
                '@type' => 'VirtualLocation',
                'url' => $meetingUrl,
            ];
        } elseif ($mode === 'hibrido' && $meetingUrl !== '') {
            $location = [
                $place,
                [
                    '@type' => 'VirtualLocation',
                    'url' => $meetingUrl,
                ],
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $title,
            'description' => $description,
            'startDate' => $startsAt,
            'eventAttendanceMode' => $attendanceMode,
            'eventStatus' => 'https://schema.org/EventScheduled',
            'location' => $location,
            'organizer' => [
                '@type' => 'Organization',
                'name' => trim((string) ($_ENV['APP_DEFAULT_SITE_NAME'] ?? 'NatalCode')),
                'url' => $baseUrl . '/',
            ],
            'url' => $pageUrl,
        ];

        if ($endsAt !== null) {
            $schema['endDate'] = $endsAt;
        }

        if ($meetingUrl !== '') {
            $schema['offers'] = [
                '@type' => 'Offer',
                'url' => $meetingUrl,
                'availability' => 'https://schema.org/InStock',
            ];
        }

        return $schema;
    }

    private function normalizeDateTimeForSchema(string $value): ?string
    {
        $normalizedValue = trim($value);
        if ($normalizedValue === '') {
            return null;
        }

        try {
            return (new \DateTimeImmutable($normalizedValue))->format(\DateTimeInterface::ATOM);
        } catch (\Throwable $exception) {
            return null;
        }
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function storeSessionFlash(string $key, array $payload): void
    {
        $this->ensureSessionStarted();

        if (!isset($_SESSION['_codex_flash']) || !is_array($_SESSION['_codex_flash'])) {
            $_SESSION['_codex_flash'] = [];
        }

        $_SESSION['_codex_flash'][$key] = $payload;
    }

    /**
     * @return array<string, mixed>
     */
    protected function consumeSessionFlash(string $key): array
    {
        $this->ensureSessionStarted();

        $flashBag = $_SESSION['_codex_flash'] ?? [];
        if (!is_array($flashBag)) {
            return [];
        }

        $payload = $flashBag[$key] ?? [];
        unset($_SESSION['_codex_flash'][$key]);

        return is_array($payload) ? $payload : [];
    }

    protected function ensureSessionStarted(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
    }

    /**
     * @return array{ok: bool, message: string, score: float|null, error_codes: list<string>}
     */
    protected function verifyRecaptchaToken(
        Request $request,
        RecaptchaVerifier $recaptchaVerifier,
        string $token,
        string $expectedAction
    ): array {
        return $recaptchaVerifier->verifySubmission(
            $token,
            $expectedAction,
            strtolower(trim($request->getUri()->getHost())),
            $this->resolveClientIp($request)
        );
    }

    protected function resolveClientIp(Request $request): ?string
    {
        $forwardedFor = trim($request->getHeaderLine('X-Forwarded-For'));
        if ($forwardedFor !== '') {
            $candidates = array_map('trim', explode(',', $forwardedFor));
            foreach ($candidates as $candidate) {
                if (filter_var($candidate, FILTER_VALIDATE_IP) !== false) {
                    return $candidate;
                }
            }
        }

        $remoteAddress = trim((string) ($request->getServerParams()['REMOTE_ADDR'] ?? ''));

        return filter_var($remoteAddress, FILTER_VALIDATE_IP) !== false
            ? $remoteAddress
            : null;
    }
}
