<?php

declare(strict_types=1);

namespace App\Domain\Institutional;

interface InstitutionalContentRepository
{
    /**
     * @return array<string, mixed>|null
     */
    public function findBySlug(string $slug): ?array;

    public function upsertBySlug(string $slug, string $title, string $body, ?int $updatedByMemberId = null): bool;
}
