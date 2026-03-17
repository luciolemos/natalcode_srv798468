<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Institutional;

use App\Domain\Institutional\InstitutionalContentRepository;

class MySqlInstitutionalContentRepository implements InstitutionalContentRepository
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findBySlug(string $slug): ?array
    {
        $normalizedSlug = $this->normalizeSlug($slug);

        if ($normalizedSlug === '') {
            return null;
        }

        $reader = function () use ($normalizedSlug): ?array {
            $sql = <<<SQL
                SELECT
                    id,
                    slug,
                    title,
                    body,
                    updated_by_member_id,
                    created_at,
                    updated_at
                FROM institutional_contents
                WHERE slug = :slug
                LIMIT 1
            SQL;

            $statement = $this->pdo->prepare($sql);
            $statement->execute(['slug' => $normalizedSlug]);
            $row = $statement->fetch();

            if (!$row) {
                return null;
            }

            return $this->normalizeRow($row);
        };

        try {
            return $reader();
        } catch (\Throwable $exception) {
            $this->ensureSchemaCompatibility();

            return $reader();
        }
    }

    public function upsertBySlug(string $slug, string $title, string $body, ?int $updatedByMemberId = null): bool
    {
        $normalizedSlug = $this->normalizeSlug($slug);
        $normalizedTitle = trim($title);
        $normalizedBody = trim($body);

        if ($normalizedSlug === '' || $normalizedTitle === '' || $normalizedBody === '') {
            return false;
        }

        $writer = function () use (
            $normalizedSlug,
            $normalizedTitle,
            $normalizedBody,
            $updatedByMemberId
        ): bool {
            $sql = <<<SQL
                INSERT INTO institutional_contents (
                    slug,
                    title,
                    body,
                    updated_by_member_id
                ) VALUES (
                    :slug,
                    :title,
                    :body,
                    :updated_by_member_id
                )
                ON DUPLICATE KEY UPDATE
                    title = VALUES(title),
                    body = VALUES(body),
                    updated_by_member_id = VALUES(updated_by_member_id),
                    updated_at = CURRENT_TIMESTAMP
            SQL;

            $statement = $this->pdo->prepare($sql);

            return $statement->execute([
                'slug' => $normalizedSlug,
                'title' => $normalizedTitle,
                'body' => $normalizedBody,
                'updated_by_member_id' => $updatedByMemberId !== null && $updatedByMemberId > 0
                    ? $updatedByMemberId
                    : null,
            ]);
        };

        try {
            return $writer();
        } catch (\Throwable $exception) {
            $this->ensureSchemaCompatibility();

            return $writer();
        }
    }

    private function normalizeSlug(string $slug): string
    {
        return strtolower(trim($slug));
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function normalizeRow(array $row): array
    {
        return [
            'id' => (int) ($row['id'] ?? 0),
            'slug' => (string) ($row['slug'] ?? ''),
            'title' => (string) ($row['title'] ?? ''),
            'body' => (string) ($row['body'] ?? ''),
            'updated_by_member_id' => $row['updated_by_member_id'] !== null
                ? (int) $row['updated_by_member_id']
                : null,
            'created_at' => (string) ($row['created_at'] ?? ''),
            'updated_at' => (string) ($row['updated_at'] ?? ''),
        ];
    }

    private function ensureSchemaCompatibility(): void
    {
        $sql = <<<SQL
            CREATE TABLE IF NOT EXISTS institutional_contents (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                slug VARCHAR(120) NOT NULL,
                title VARCHAR(255) NOT NULL,
                body LONGTEXT NOT NULL,
                updated_by_member_id BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_institutional_contents_slug (slug)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL;

        $this->pdo->exec($sql);
    }
}
