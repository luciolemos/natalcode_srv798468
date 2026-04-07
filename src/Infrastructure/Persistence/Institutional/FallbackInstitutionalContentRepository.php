<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Institutional;

use App\Domain\Institutional\InstitutionalContentRepository;

class FallbackInstitutionalContentRepository implements InstitutionalContentRepository
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private array $items = [];

    public function findBySlug(string $slug): ?array
    {
        $normalizedSlug = strtolower(trim($slug));

        if ($normalizedSlug === '') {
            return null;
        }

        return $this->items[$normalizedSlug] ?? null;
    }

    public function upsertBySlug(string $slug, string $title, string $body, ?int $updatedByMemberId = null): bool
    {
        $normalizedSlug = strtolower(trim($slug));
        $normalizedTitle = trim($title);
        $normalizedBody = trim($body);

        if ($normalizedSlug === '' || $normalizedTitle === '' || $normalizedBody === '') {
            return false;
        }

        $current = $this->items[$normalizedSlug] ?? null;
        $createdAt = (string) ($current['created_at'] ?? date('Y-m-d H:i:s'));

        $this->items[$normalizedSlug] = [
            'id' => (int) ($current['id'] ?? (count($this->items) + 1)),
            'slug' => $normalizedSlug,
            'title' => $normalizedTitle,
            'body' => $normalizedBody,
            'updated_by_member_id' => $updatedByMemberId,
            'created_at' => $createdAt,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        return true;
    }
}
