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

    private const SORT_FIELDS = ['submitted_at', 'request_protocol', 'name', 'email', 'subject'];

    private const BASE_PATH = '/painel/institucional/solicitacoes-contato';

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
        $queryParams = $request->getQueryParams();
        $searchTerm = trim((string) ($queryParams['q'] ?? ''));
        $status = '';
        $errorMessage = '';
        $contactRequests = [];

        try {
            $contactRequests = $this->contactRequestRepository->findAllForAdmin();
        } catch (\Throwable $exception) {
            $status = 'load-error';
            $errorMessage = 'Não foi possível carregar as solicitações de contato no momento.';

            $this->logger->error('Falha ao carregar solicitações de contato no painel.', [
                'exception' => $exception,
            ]);
        }

        $contactRequests = array_map(function (array $requestRow): array {
            $submittedAt = trim((string) ($requestRow['submitted_at'] ?? ''));
            $requestRow['submitted_at_label'] = $this->formatDateTimeLabel($submittedAt);

            $message = trim((string) ($requestRow['message'] ?? ''));
            $requestRow['message_preview'] = $this->buildMessagePreview($message);

            return $requestRow;
        }, $contactRequests);

        if ($searchTerm !== '') {
            $normalizedSearch = strtolower($searchTerm);
            $contactRequests = array_values(array_filter(
                $contactRequests,
                static function (array $requestRow) use ($normalizedSearch): bool {
                    $haystack = implode(' ', [
                        (string) ($requestRow['request_protocol'] ?? ''),
                        (string) ($requestRow['request_id'] ?? ''),
                        (string) ($requestRow['submitted_at_label'] ?? ''),
                        (string) ($requestRow['name'] ?? ''),
                        (string) ($requestRow['email'] ?? ''),
                        (string) ($requestRow['segment'] ?? ''),
                        (string) ($requestRow['subject'] ?? ''),
                        (string) ($requestRow['message'] ?? ''),
                        (string) ($requestRow['origin_url'] ?? ''),
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
                $firstTimestamp = strtotime((string) ($firstRow['submitted_at'] ?? '')) ?: 0;
                $secondTimestamp = strtotime((string) ($secondRow['submitted_at'] ?? '')) ?: 0;

                return ($firstTimestamp <=> $secondTimestamp) * $sortMultiplier;
            }

            $firstValue = (string) ($firstRow[$sortBy] ?? '');
            $secondValue = (string) ($secondRow[$sortBy] ?? '');

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

        $startItem = $totalItems > 0 ? $offset + 1 : 0;
        $endItem = $totalItems > 0 ? min($offset + count($contactRequests), $totalItems) : 0;

        $pageSizeQueryValue = $showAllItems ? self::ALL_PAGE_SIZE : (string) $pageSize;
        $baseQuery = [
            'per_page' => $pageSizeQueryValue,
            'sort' => $sortBy,
            'dir' => $sortDirection,
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
                'q' => $searchTerm,
            ]),
        ];

        return $this->renderPage($response, 'pages/admin-contact-requests.twig', [
            'admin_contact_requests' => $contactRequests,
            'admin_contact_requests_status' => $status,
            'admin_contact_requests_error' => $errorMessage,
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
}

