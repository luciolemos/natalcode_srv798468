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
     *   user_agent: string
     * }>
     */
    public function findAllForAdmin(): array;
}

