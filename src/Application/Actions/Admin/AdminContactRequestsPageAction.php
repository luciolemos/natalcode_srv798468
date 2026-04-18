<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\Page\AbstractPageAction;
use App\Domain\Contact\ContactRequestRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class AdminContactRequestsPageAction extends AbstractPageAction
{
    private const DEFAULT_PAGE_SIZE = 20;

    private const PAGE_SIZE_OPTIONS = [10, 20, 50, 100];

    private const ALL_PAGE_SIZE = 'all';

    private const SORT_FIELDS = ['submitted_at', 'request_protocol', 'name', 'email', 'subject', 'status'];

    private const BASE_PATH = '/painel/institucional/solicitacoes-contato';

    private const FLASH_KEY = 'admin_contact_requests';

    private const STATUS_FILTER_ALL = 'all';

    /** @var array<string, string> */
    private const STATUS_LABELS = [
        'novo' => 'Novo',
        'lido' => 'Lido',
        'em_analise' => 'Em análise',
        'contatado' => 'Contatado',
        'pendente_retorno' => 'Pendente de retorno',
        'aguardando_cliente' => 'Aguardando cliente',
        'concluido' => 'Concluído',
    ];

    private ContactRequestRepository $contactRequestRepository;

    public function __construct(
        LoggerInterface $logger,
        Twig $twig,
        ContactRequestRepository $contactRequestRepository
    ) {
        parent::__construct($logger, $twig);
        $this->contactRequestRepository = $contactRequestRepository;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $method = strtoupper($request->getMethod());
        if ($method === 'POST') {
            return $this->handleStatusUpdate($request, $response);
        }

        $flash = $this->consumeSessionFlash(self::FLASH_KEY);
        $status = trim((string) ($flash['status'] ?? ''));
        $feedbackMessage = trim((string) ($flash['message'] ?? ''));

        $queryParams = $request->getQueryParams();
        $searchTerm = trim((string) ($queryParams['q'] ?? ''));
        $statusFilter = $this->normalizeStatusFilter((string) ($queryParams['status'] ?? self::STATUS_FILTER_ALL));
        $errorMessage = $feedbackMessage;
        $contactRequests = [];

        try {
            $contactRequests = $this->contactRequestRepository->findAllForAdmin();
        } catch (\Throwable $exception) {
            $status = 'load-error';
            if ($errorMessage === '') {
                $errorMessage = 'Não foi possível carregar as solicitações de contato no momento.';
            }

            $this->logger->error('Falha ao carregar solicitações de contato no painel.', [
                'exception' => $exception,
            ]);
        }

        $contactRequests = array_map(function (array $requestRow): array {
            $submittedAt = trim((string) $requestRow['submitted_at']);
            $requestRow['submitted_at_label'] = $this->formatDateTimeLabel($submittedAt);

            $message = trim((string) $requestRow['message']);
            $requestRow['message_preview'] = $this->buildMessagePreview($message);

            $requestRow['status'] = $this->normalizeStatusValue((string) $requestRow['status']);
            $requestRow['status_label'] = $this->resolveStatusLabel($requestRow['status']);
            $requestRow['status_badge_modifier'] = $this->resolveStatusBadgeModifier($requestRow['status']);

            $statusUpdatedAt = trim((string) $requestRow['status_updated_at']);
            $requestRow['status_updated_at_label'] = $this->formatDateTimeLabel($statusUpdatedAt);
            $requestRow['status_updated_by_member_id'] = isset($requestRow['status_updated_by_member_id'])
                && $requestRow['status_updated_by_member_id'] !== null
                ? (int) $requestRow['status_updated_by_member_id']
                : null;
            $requestRow['status_updated_by_name'] = trim((string) $requestRow['status_updated_by_name']);
            $requestRow['status_updated_by_label'] = $this->resolveActorLabel(
                $requestRow['status_updated_by_name'],
                $requestRow['status_updated_by_member_id']
            );
            $requestRow['events'] = [];

            return $requestRow;
        }, $contactRequests);

        if ($statusFilter !== self::STATUS_FILTER_ALL) {
            $contactRequests = array_values(array_filter(
                $contactRequests,
                static fn (array $requestRow): bool => (string) $requestRow['status'] === $statusFilter
            ));
        }

        if ($searchTerm !== '') {
            $normalizedSearch = strtolower($searchTerm);
            $contactRequests = array_values(array_filter(
                $contactRequests,
                static function (array $requestRow) use ($normalizedSearch): bool {
                    $haystack = implode(' ', [
                        (string) $requestRow['request_protocol'],
                        (string) $requestRow['request_id'],
                        (string) $requestRow['submitted_at_label'],
                        (string) $requestRow['name'],
                        (string) $requestRow['email'],
                        (string) $requestRow['segment'],
                        (string) $requestRow['subject'],
                        (string) $requestRow['message'],
                        (string) $requestRow['origin_url'],
                        (string) $requestRow['status_label'],
                    ]);

                    return stripos(strtolower($haystack), $normalizedSearch) !== false;
                }
            ));
        }

        $sortBy = (string) ($queryParams['sort'] ?? 'submitted_at');
        if (!in_array($sortBy, self::SORT_FIELDS, true)) {
            $sortBy = 'submitted_at';
        }

        $sortDirection = strtolower((string) ($queryParams['dir'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';
        $sortMultiplier = $sortDirection === 'desc' ? -1 : 1;

        usort($contactRequests, static function (array $firstRow, array $secondRow) use ($sortBy, $sortMultiplier): int {
            if ($sortBy === 'submitted_at') {
                $firstTimestamp = strtotime((string) $firstRow['submitted_at']) ?: 0;
                $secondTimestamp = strtotime((string) $secondRow['submitted_at']) ?: 0;

                return ($firstTimestamp <=> $secondTimestamp) * $sortMultiplier;
            }

            $firstValue = (string) $firstRow[$sortBy];
            $secondValue = (string) $secondRow[$sortBy];

            return strnatcasecmp($firstValue, $secondValue) * $sortMultiplier;
        });

        $totalItems = count($contactRequests);
        $requestedPageSize = trim((string) ($queryParams['per_page'] ?? (string) self::DEFAULT_PAGE_SIZE));
        $showAllItems = $requestedPageSize === self::ALL_PAGE_SIZE;
        $pageSize = self::DEFAULT_PAGE_SIZE;

        if (!$showAllItems) {
            $requestedPageSizeNumber = (int) $requestedPageSize;
            $pageSize = in_array($requestedPageSizeNumber, self::PAGE_SIZE_OPTIONS, true)
                ? $requestedPageSizeNumber
                : self::DEFAULT_PAGE_SIZE;
        } else {
            $pageSize = max($totalItems, 1);
        }

        $totalPages = max(1, (int) ceil($totalItems / $pageSize));
        $currentPage = max(1, (int) ($queryParams['page'] ?? 1));
        $currentPage = min($currentPage, $totalPages);

        $offset = ($currentPage - 1) * $pageSize;
        $contactRequests = array_slice($contactRequests, $offset, $pageSize);

        $requestIdsOnCurrentPage = array_values(array_filter(
            array_map(static fn (array $requestRow): int => (int) $requestRow['id'], $contactRequests),
            static fn (int $requestId): bool => $requestId > 0
        ));

        $eventsByRequest = [];
        if ($requestIdsOnCurrentPage !== []) {
            try {
                $eventsByRequest = $this->contactRequestRepository->findEventsForAdmin($requestIdsOnCurrentPage);
            } catch (\Throwable $exception) {
                $this->logger->warning('Falha ao carregar histórico de solicitações de contato.', [
                    'exception' => $exception,
                ]);
            }
        }

        $contactRequests = array_map(function (array $requestRow) use ($eventsByRequest): array {
            $requestId = (int) $requestRow['id'];
            $events = $eventsByRequest[$requestId] ?? [];

            $requestRow['events'] = array_map(function (array $event): array {
                $event['created_at_label'] = $this->formatDateTimeLabel((string) $event['created_at']);
                $event['previous_status_label'] = $this->resolveStatusLabel((string) $event['previous_status']);
                $event['next_status_label'] = $this->resolveStatusLabel((string) $event['next_status']);
                $event['actor_label'] = $this->resolveActorLabel(
                    trim((string) $event['actor_name']),
                    isset($event['actor_member_id']) && $event['actor_member_id'] !== null
                        ? (int) $event['actor_member_id']
                        : null
                );
                $event['note'] = trim((string) $event['note']);

                return $event;
            }, $events);

            return $requestRow;
        }, $contactRequests);

        $startItem = $totalItems > 0 ? $offset + 1 : 0;
        $endItem = $totalItems > 0 ? min($offset + count($contactRequests), $totalItems) : 0;

        $pageSizeQueryValue = $showAllItems ? self::ALL_PAGE_SIZE : (string) $pageSize;
        $baseQuery = [
            'per_page' => $pageSizeQueryValue,
            'sort' => $sortBy,
            'dir' => $sortDirection,
            'status' => $statusFilter,
        ];
        if ($searchTerm !== '') {
            $baseQuery['q'] = $searchTerm;
        }

        $sortLinks = [];
        foreach (self::SORT_FIELDS as $field) {
            $nextDirection = $sortBy === $field && $sortDirection === 'asc' ? 'desc' : 'asc';
            $indicator = '↕';

            if ($sortBy === $field) {
                $indicator = $sortDirection === 'asc' ? '↑' : '↓';
            }

            $sortLinks[$field] = [
                'url' => self::BASE_PATH . '?' . http_build_query([
                    'page' => 1,
                    'per_page' => $pageSizeQueryValue,
                    'sort' => $field,
                    'dir' => $nextDirection,
                    'status' => $statusFilter,
                    'q' => $searchTerm,
                ]),
                'indicator' => $indicator,
                'active' => $sortBy === $field,
            ];
        }

        $paginationLinks = [];
        for ($page = 1; $page <= $totalPages; $page++) {
            $paginationLinks[] = [
                'number' => $page,
                'active' => $page === $currentPage,
                'url' => self::BASE_PATH . '?' . http_build_query(array_merge($baseQuery, ['page' => $page])),
            ];
        }

        $previousPageUrl = $currentPage > 1
            ? self::BASE_PATH . '?' . http_build_query(array_merge($baseQuery, ['page' => $currentPage - 1]))
            : null;
        $nextPageUrl = $currentPage < $totalPages
            ? self::BASE_PATH . '?' . http_build_query(array_merge($baseQuery, ['page' => $currentPage + 1]))
            : null;

        $pageSizeOptions = array_map(static fn (int $option): array => [
            'value' => (string) $option,
            'label' => (string) $option,
            'selected' => !$showAllItems && $option === $pageSize,
            'url' => self::BASE_PATH . '?' . http_build_query([
                'page' => 1,
                'per_page' => $option,
                'sort' => $sortBy,
                'dir' => $sortDirection,
                'status' => $statusFilter,
                'q' => $searchTerm,
            ]),
        ], self::PAGE_SIZE_OPTIONS);
        $pageSizeOptions[] = [
            'value' => self::ALL_PAGE_SIZE,
            'label' => 'Todos',
            'selected' => $showAllItems,
            'url' => self::BASE_PATH . '?' . http_build_query([
                'page' => 1,
                'per_page' => self::ALL_PAGE_SIZE,
                'sort' => $sortBy,
                'dir' => $sortDirection,
                'status' => $statusFilter,
                'q' => $searchTerm,
            ]),
        ];

        $currentUrl = self::BASE_PATH;
        $currentQuery = trim((string) $request->getUri()->getQuery());
        if ($currentQuery !== '') {
            $currentUrl .= '?' . $currentQuery;
        }

        return $this->renderPage($response, 'pages/admin-contact-requests.twig', [
            'admin_contact_requests' => $contactRequests,
            'admin_contact_requests_status' => $status,
            'admin_contact_requests_error' => $errorMessage,
            'admin_contact_requests_filter_status' => $statusFilter,
            'admin_contact_requests_status_options' => $this->buildStatusFilterOptions($statusFilter),
            'admin_contact_requests_current_url' => $currentUrl,
            'admin_contact_requests_search' => $searchTerm,
            'admin_contact_requests_sort_links' => $sortLinks,
            'admin_contact_requests_pagination' => [
                'current_page' => $currentPage,
                'total_pages' => $totalPages,
                'total_items' => $totalItems,
                'start_item' => $startItem,
                'end_item' => $endItem,
                'page_size' => $pageSize,
                'sort' => $sortBy,
                'dir' => $sortDirection,
                'status' => $statusFilter,
                'links' => $paginationLinks,
                'previous_url' => $previousPageUrl,
                'next_url' => $nextPageUrl,
                'page_size_options' => $pageSizeOptions,
            ],
            'page_title' => 'Solicitações de contato | Painel NatalCode',
            'page_url' => 'https://natalcode.com.br' . self::BASE_PATH,
            'page_description' => 'Controle das solicitações recebidas no formulário de contato.',
        ]);
    }

    private function handleStatusUpdate(Request $request, Response $response): Response
    {
        $body = (array) ($request->getParsedBody() ?? []);
        $requestId = (int) ($body['request_id'] ?? 0);
        $newStatus = $this->sanitizeStatusFromInput((string) ($body['status'] ?? ''));
        $note = trim((string) ($body['note'] ?? ''));
        $redirectTarget = $this->sanitizeReturnTo((string) ($body['return_to'] ?? ''));

        if ($requestId <= 0 || $newStatus === '') {
            $this->storeSessionFlash(self::FLASH_KEY, [
                'status' => 'update-error',
                'message' => 'Solicitação inválida para atualização de status.',
            ]);

            return $response->withHeader('Location', $redirectTarget)->withStatus(303);
        }

        $actorMemberId = (int) ($_SESSION['member_user_id'] ?? 0);
        $actorName = trim((string) ($_SESSION['member_name'] ?? ''));

        try {
            $updated = $this->contactRequestRepository->updateStatusForAdmin(
                $requestId,
                $newStatus,
                $actorMemberId > 0 ? $actorMemberId : null,
                $actorName,
                $note
            );

            if ($updated) {
                $this->storeSessionFlash(self::FLASH_KEY, [
                    'status' => 'status-updated',
                    'message' => 'Status da solicitação atualizado com sucesso.',
                ]);
            } else {
                $this->storeSessionFlash(self::FLASH_KEY, [
                    'status' => 'update-error',
                    'message' => 'Não foi possível atualizar o status da solicitação.',
                ]);
            }
        } catch (\Throwable $exception) {
            $this->logger->error('Falha ao atualizar status da solicitação de contato.', [
                'request_id' => $requestId,
                'status' => $newStatus,
                'exception' => $exception,
            ]);

            $this->storeSessionFlash(self::FLASH_KEY, [
                'status' => 'update-error',
                'message' => 'Erro ao atualizar o status da solicitação. Tente novamente.',
            ]);
        }

        return $response->withHeader('Location', $redirectTarget)->withStatus(303);
    }

    private function formatDateTimeLabel(string $dateTime): string
    {
        $normalized = trim($dateTime);
        if ($normalized === '') {
            return '-';
        }

        $dateTimeObject = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $normalized);
        if ($dateTimeObject instanceof \DateTimeImmutable) {
            return $dateTimeObject->format('d/m/Y H:i:s');
        }

        return $normalized;
    }

    private function buildMessagePreview(string $message): string
    {
        $normalized = str_replace(["\r\n", "\r"], "\n", trim($message));
        $normalized = preg_replace("/\s+/", ' ', $normalized) ?? $normalized;
        $normalized = trim($normalized);

        if ($normalized === '') {
            return '-';
        }

        if (mb_strlen($normalized) <= 120) {
            return $normalized;
        }

        return mb_substr($normalized, 0, 117) . '...';
    }

    private function sanitizeReturnTo(string $returnTo): string
    {
        $returnTo = trim($returnTo);
        if ($returnTo === '') {
            return self::BASE_PATH;
        }

        $parts = parse_url($returnTo);
        $path = trim((string) ($parts['path'] ?? ''));
        if ($path === '' || !str_starts_with($path, self::BASE_PATH)) {
            return self::BASE_PATH;
        }

        $query = trim((string) ($parts['query'] ?? ''));

        return $query !== '' ? $path . '?' . $query : $path;
    }

    private function normalizeStatusFilter(string $status): string
    {
        $normalized = strtolower(trim($status));
        if ($normalized === '' || $normalized === self::STATUS_FILTER_ALL) {
            return self::STATUS_FILTER_ALL;
        }

        return array_key_exists($normalized, self::STATUS_LABELS)
            ? $normalized
            : self::STATUS_FILTER_ALL;
    }

    private function normalizeStatusValue(string $status): string
    {
        $normalized = strtolower(trim($status));

        return array_key_exists($normalized, self::STATUS_LABELS)
            ? $normalized
            : 'novo';
    }

    private function sanitizeStatusFromInput(string $status): string
    {
        $normalized = strtolower(trim($status));

        return array_key_exists($normalized, self::STATUS_LABELS)
            ? $normalized
            : '';
    }

    /**
     * @return array<int, array{value: string, label: string, selected: bool}>
     */
    private function buildStatusFilterOptions(string $currentStatus): array
    {
        $options = [[
            'value' => self::STATUS_FILTER_ALL,
            'label' => 'Todos os status',
            'selected' => $currentStatus === self::STATUS_FILTER_ALL,
        ]];

        foreach (self::STATUS_LABELS as $statusKey => $statusLabel) {
            $options[] = [
                'value' => $statusKey,
                'label' => $statusLabel,
                'selected' => $currentStatus === $statusKey,
            ];
        }

        return $options;
    }

    private function resolveStatusLabel(string $status): string
    {
        $normalized = strtolower(trim($status));

        return self::STATUS_LABELS[$normalized] ?? self::STATUS_LABELS['novo'];
    }

    private function resolveStatusBadgeModifier(string $status): string
    {
        return match ($status) {
            'concluido' => 'is-on',
            'contatado' => 'is-progress',
            'pendente_retorno', 'aguardando_cliente' => 'is-warn',
            default => 'is-off',
        };
    }

    private function resolveActorLabel(string $actorName, ?int $actorMemberId): string
    {
        if ($actorName !== '') {
            return $actorName;
        }

        if ($actorMemberId !== null && $actorMemberId > 0) {
            return 'Membro #' . $actorMemberId;
        }

        return '-';
    }
}
