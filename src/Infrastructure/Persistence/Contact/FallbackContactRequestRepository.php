<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Contact;

use App\Domain\Contact\ContactRequestRepository;

class FallbackContactRequestRepository implements ContactRequestRepository
{
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
     *   user_agent: string
     * }>
     */
    private array $items = [];

    private int $nextId = 1;

    public function create(array $data): int
    {
        $id = $this->nextId++;
        $this->items[] = [
            'id' => $id,
            'request_protocol' => trim((string) ($data['request_protocol'] ?? '')),
            'request_id' => trim((string) ($data['request_id'] ?? '')),
            'submitted_at' => trim((string) ($data['submitted_at'] ?? (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s'))),
            'name' => trim((string) ($data['name'] ?? '')),
            'email' => strtolower(trim((string) ($data['email'] ?? ''))),
            'segment' => trim((string) ($data['segment'] ?? '')),
            'subject' => trim((string) ($data['subject'] ?? '')),
            'message' => trim((string) ($data['message'] ?? '')),
            'origin_url' => trim((string) ($data['origin_url'] ?? '')),
            'ip_address' => trim((string) ($data['ip_address'] ?? '')),
            'user_agent' => trim((string) ($data['user_agent'] ?? '')),
        ];

        return $id;
    }

    public function findAllForAdmin(): array
    {
        $items = $this->items;

        usort($items, static function (array $left, array $right): int {
            $dateComparison = strcmp((string) ($right['submitted_at'] ?? ''), (string) ($left['submitted_at'] ?? ''));
            if ($dateComparison !== 0) {
                return $dateComparison;
            }

            return ((int) ($right['id'] ?? 0)) <=> ((int) ($left['id'] ?? 0));
        });

        return $items;
    }
}

