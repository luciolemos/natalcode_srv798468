<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Contact;

use App\Domain\Contact\ContactRequestRepository;

class MySqlContactRequestRepository implements ContactRequestRepository
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(array $data): int
    {
        try {
            $this->persistContactRequest($data);
        } catch (\Throwable $exception) {
            $this->ensureSchemaCompatibility();
            $this->persistContactRequest($data);
        }

        return (int) $this->pdo->lastInsertId();
    }

    public function findAllForAdmin(): array
    {
        try {
            return $this->fetchAllForAdmin();
        } catch (\Throwable $exception) {
            $this->ensureSchemaCompatibility();

            return $this->fetchAllForAdmin();
        }
    }

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
    private function persistContactRequest(array $data): void
    {
        $statement = $this->pdo->prepare(<<<SQL
            INSERT INTO contact_requests (
                request_protocol,
                request_id,
                submitted_at,
                name,
                email,
                segment,
                subject,
                message,
                origin_url,
                ip_address,
                user_agent
            ) VALUES (
                :request_protocol,
                :request_id,
                :submitted_at,
                :name,
                :email,
                :segment,
                :subject,
                :message,
                :origin_url,
                :ip_address,
                :user_agent
            )
        SQL);

        $statement->execute([
            'request_protocol' => $this->normalizeLine((string) ($data['request_protocol'] ?? ''), 32),
            'request_id' => $this->normalizeLine((string) ($data['request_id'] ?? ''), 80),
            'submitted_at' => $this->normalizeDateTime((string) ($data['submitted_at'] ?? '')),
            'name' => $this->normalizeLine((string) ($data['name'] ?? ''), 160),
            'email' => strtolower($this->normalizeLine((string) ($data['email'] ?? ''), 190)),
            'segment' => $this->nullableLine((string) ($data['segment'] ?? ''), 120),
            'subject' => $this->normalizeLine((string) ($data['subject'] ?? ''), 190),
            'message' => $this->normalizeText((string) ($data['message'] ?? '')),
            'origin_url' => $this->nullableLine((string) ($data['origin_url'] ?? ''), 255),
            'ip_address' => $this->nullableLine((string) ($data['ip_address'] ?? ''), 64),
            'user_agent' => $this->nullableLine((string) ($data['user_agent'] ?? ''), 255),
        ]);
    }

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
    private function fetchAllForAdmin(): array
    {
        $statement = $this->pdo->query(<<<SQL
            SELECT
                id,
                request_protocol,
                request_id,
                submitted_at,
                name,
                email,
                COALESCE(segment, '') AS segment,
                subject,
                message,
                COALESCE(origin_url, '') AS origin_url,
                COALESCE(ip_address, '') AS ip_address,
                COALESCE(user_agent, '') AS user_agent
            FROM contact_requests
            ORDER BY submitted_at DESC, id DESC
        SQL);

        $rows = $statement->fetchAll() ?: [];

        return array_map(static fn (array $row): array => [
            'id' => (int) ($row['id'] ?? 0),
            'request_protocol' => (string) ($row['request_protocol'] ?? ''),
            'request_id' => (string) ($row['request_id'] ?? ''),
            'submitted_at' => (string) ($row['submitted_at'] ?? ''),
            'name' => (string) ($row['name'] ?? ''),
            'email' => (string) ($row['email'] ?? ''),
            'segment' => (string) ($row['segment'] ?? ''),
            'subject' => (string) ($row['subject'] ?? ''),
            'message' => (string) ($row['message'] ?? ''),
            'origin_url' => (string) ($row['origin_url'] ?? ''),
            'ip_address' => (string) ($row['ip_address'] ?? ''),
            'user_agent' => (string) ($row['user_agent'] ?? ''),
        ], $rows);
    }

    private function ensureSchemaCompatibility(): void
    {
        $this->pdo->exec(<<<SQL
            CREATE TABLE IF NOT EXISTS contact_requests (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                request_protocol VARCHAR(32) NOT NULL,
                request_id VARCHAR(80) NOT NULL,
                submitted_at DATETIME NOT NULL,
                name VARCHAR(160) NOT NULL,
                email VARCHAR(190) NOT NULL,
                segment VARCHAR(120) NULL,
                subject VARCHAR(190) NOT NULL,
                message TEXT NOT NULL,
                origin_url VARCHAR(255) NULL,
                ip_address VARCHAR(64) NULL,
                user_agent VARCHAR(255) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_contact_requests_protocol (request_protocol),
                UNIQUE KEY uq_contact_requests_request_id (request_id),
                KEY idx_contact_requests_submitted_at (submitted_at),
                KEY idx_contact_requests_email (email),
                KEY idx_contact_requests_subject (subject)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);
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

    private function nullableLine(string $value, int $maxLength): ?string
    {
        $normalized = $this->normalizeLine($value, $maxLength);

        return $normalized === '' ? null : $normalized;
    }

    private function normalizeText(string $value): string
    {
        $normalized = str_replace(["\r\n", "\r"], "\n", $value);
        $normalized = preg_replace("/\n{3,}/", "\n\n", $normalized) ?? $normalized;
        $normalized = trim($normalized);

        return $normalized !== '' ? $normalized : 'Mensagem nao informada.';
    }

    private function normalizeDateTime(string $value): string
    {
        $normalized = trim($value);
        if ($normalized === '') {
            return (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s');
        }

        $parsed = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $normalized);
        if ($parsed instanceof \DateTimeImmutable) {
            return $parsed->format('Y-m-d H:i:s');
        }

        return (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s');
    }
}

