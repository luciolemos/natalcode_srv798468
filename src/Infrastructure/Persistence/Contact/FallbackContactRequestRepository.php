<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Contact;

use App\Domain\Contact\ContactRequestRepository;

class FallbackContactRequestRepository implements ContactRequestRepository
{
    private const DEFAULT_STATUS = 'novo';

    private const EVENT_TYPE_CREATED = 'created';

    private const EVENT_TYPE_STATUS_CHANGED = 'status_changed';

    /**
     * @var array<int, array{
     *   id: int,
     *   request_protocol: string,
     *   request_id: string,
     *   submitted_at: string,
     *   name: string,
     *   email: string,
     *   segment: string,
     *   subject: string,
     *   message: string,
     *   origin_url: string,
     *   ip_address: string,
     *   user_agent: string,
     *   status: string,
     *   status_updated_at: string,
     *   status_updated_by_member_id: int|null,
     *   status_updated_by_name: string
     * }>
     */
    private array $items = [];

    /**
     * @var array<int, list<array{
     *   id: int,
     *   contact_request_id: int,
     *   event_type: string,
     *   previous_status: string,
     *   next_status: string,
     *   note: string,
     *   actor_member_id: int|null,
     *   actor_name: string,
     *   created_at: string
     * }>>
     */
    private array $eventsByRequest = [];

    private int $nextId = 1;

    private int $nextEventId = 1;

    public function create(array $data): int
    {
        $id = $this->nextId++;
        $submittedAt = trim((string) $data['submitted_at']);
        $this->items[] = [
            'id' => $id,
            'request_protocol' => trim((string) $data['request_protocol']),
            'request_id' => trim((string) $data['request_id']),
            'submitted_at' => $submittedAt,
            'name' => trim((string) $data['name']),
            'email' => strtolower(trim((string) $data['email'])),
            'segment' => trim((string) $data['segment']),
            'subject' => trim((string) $data['subject']),
            'message' => trim((string) $data['message']),
            'origin_url' => trim((string) ($data['origin_url'] ?? '')),
            'ip_address' => trim((string) ($data['ip_address'] ?? '')),
            'user_agent' => trim((string) ($data['user_agent'] ?? '')),
            'status' => self::DEFAULT_STATUS,
            'status_updated_at' => $submittedAt,
            'status_updated_by_member_id' => null,
            'status_updated_by_name' => '',
        ];
        $this->appendEvent(
            $id,
            self::EVENT_TYPE_CREATED,
            '',
            self::DEFAULT_STATUS,
            null,
            '',
            '',
            $submittedAt
        );

        return $id;
    }

    public function findAllForAdmin(): array
    {
        $items = $this->items;

        usort($items, static function (array $left, array $right): int {
            $dateComparison = strcmp((string) $right['submitted_at'], (string) $left['submitted_at']);
            if ($dateComparison !== 0) {
                return $dateComparison;
            }

            return ((int) $right['id']) <=> ((int) $left['id']);
        });

        return $items;
    }

    /**
     * @param array<int, int> $requestIds
     */
    public function findEventsForAdmin(array $requestIds): array
    {
        $normalizedIds = $this->normalizeRequestIds($requestIds);
        if ($normalizedIds === []) {
            return [];
        }

        $events = [];
        foreach ($normalizedIds as $requestId) {
            $events[$requestId] = $this->eventsByRequest[$requestId] ?? [];
        }

        return $events;
    }

    public function updateStatusForAdmin(
        int $requestId,
        string $status,
        ?int $actorMemberId = null,
        string $actorName = '',
        string $note = ''
    ): bool {
        if ($requestId <= 0) {
            return false;
        }

        $normalizedStatus = $this->normalizeLine($status, 32);
        if ($normalizedStatus === '') {
            return false;
        }

        $normalizedActorName = $this->normalizeLine($actorName, 160);
        $normalizedNote = $this->normalizeLine($note, 500);
        $actorId = $actorMemberId !== null && $actorMemberId > 0 ? $actorMemberId : null;
        $now = (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s');

        foreach ($this->items as $index => $item) {
            if ((int) $item['id'] !== $requestId) {
                continue;
            }

            $previousStatus = $this->normalizeLine((string) $item['status'], 32);
            if ($previousStatus === $normalizedStatus && $normalizedNote === '') {
                return true;
            }

            $this->items[$index]['status'] = $normalizedStatus;
            $this->items[$index]['status_updated_at'] = $now;
            $this->items[$index]['status_updated_by_member_id'] = $actorId;
            $this->items[$index]['status_updated_by_name'] = $normalizedActorName;

            $this->appendEvent(
                $requestId,
                self::EVENT_TYPE_STATUS_CHANGED,
                $previousStatus,
                $normalizedStatus,
                $actorId,
                $normalizedActorName,
                $normalizedNote,
                $now
            );

            return true;
        }

        return false;
    }

    private function appendEvent(
        int $requestId,
        string $eventType,
        string $previousStatus,
        string $nextStatus,
        ?int $actorMemberId,
        string $actorName,
        string $note,
        string $createdAt
    ): void {
        if (!isset($this->eventsByRequest[$requestId])) {
            $this->eventsByRequest[$requestId] = [];
        }

        array_unshift($this->eventsByRequest[$requestId], [
            'id' => $this->nextEventId++,
            'contact_request_id' => $requestId,
            'event_type' => $eventType,
            'previous_status' => $previousStatus,
            'next_status' => $nextStatus,
            'note' => $note,
            'actor_member_id' => $actorMemberId,
            'actor_name' => $actorName,
            'created_at' => $createdAt,
        ]);
    }

    /**
     * @param array<int, mixed> $requestIds
     * @return array<int, int>
     */
    private function normalizeRequestIds(array $requestIds): array
    {
        $normalizedIds = [];

        foreach ($requestIds as $requestId) {
            $id = (int) $requestId;
            if ($id > 0) {
                $normalizedIds[] = $id;
            }
        }

        $normalizedIds = array_values(array_unique($normalizedIds));

        return $normalizedIds;
    }

    private function normalizeLine(string $value, int $maxLength): string
    {
        $normalized = preg_replace('/[\r\n\t]+/', ' ', $value) ?? '';
        $normalized = preg_replace('/\s{2,}/', ' ', $normalized) ?? $normalized;
        $normalized = trim($normalized);

        if ($normalized === '') {
            return '';
        }

        return mb_substr($normalized, 0, $maxLength);
    }
}
