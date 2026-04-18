<?php

declare(strict_types=1);

namespace App\Domain\Contact;

interface ContactRequestRepository
{
    /**
     * @param array{
     *   request_protocol: string,
     *   request_id: string,
     *   submitted_at: string,
     *   name: string,
     *   email: string,
     *   segment: string,
     *   subject: string,
     *   message: string,
     *   origin_url?: string,
     *   ip_address?: string,
     *   user_agent?: string
     * } $data
     */
    public function create(array $data): int;

    /**
     * @return array<int, array{
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
    public function findAllForAdmin(): array;

    /**
     * @param array<int, int> $requestIds
     * @return array<int, list<array{
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
    public function findEventsForAdmin(array $requestIds): array;

    public function updateStatusForAdmin(
        int $requestId,
        string $status,
        ?int $actorMemberId = null,
        string $actorName = '',
        string $note = ''
    ): bool;
}
