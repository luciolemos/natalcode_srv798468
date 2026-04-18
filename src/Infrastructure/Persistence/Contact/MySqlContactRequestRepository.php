<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Contact;

use App\Domain\Contact\ContactRequestRepository;

class MySqlContactRequestRepository implements ContactRequestRepository
{
    private const DEFAULT_STATUS = 'novo';

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
     * @param array<int, int> $requestIds
     */
    public function findEventsForAdmin(array $requestIds): array
    {
        $normalizedIds = $this->normalizeRequestIds($requestIds);
        if ($normalizedIds === []) {
            return [];
        }

        try {
            return $this->fetchEventsForAdmin($normalizedIds);
        } catch (\Throwable $exception) {
            $this->ensureSchemaCompatibility();

            return $this->fetchEventsForAdmin($normalizedIds);
        }
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

        try {
            return $this->persistStatusUpdate($requestId, $status, $actorMemberId, $actorName, $note);
        } catch (\Throwable $exception) {
            $this->ensureSchemaCompatibility();

            return $this->persistStatusUpdate($requestId, $status, $actorMemberId, $actorName, $note);
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
                user_agent,
                status,
                status_updated_at,
                status_updated_by_member_id,
                status_updated_by_name
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
                :user_agent,
                :status,
                :status_updated_at,
                :status_updated_by_member_id,
                :status_updated_by_name
            )
        SQL);

        $submittedAt = $this->normalizeDateTime((string) $data['submitted_at']);

        $statement->execute([
            'request_protocol' => $this->normalizeLine((string) $data['request_protocol'], 32),
            'request_id' => $this->normalizeLine((string) $data['request_id'], 80),
            'submitted_at' => $submittedAt,
            'name' => $this->normalizeLine((string) $data['name'], 160),
            'email' => strtolower($this->normalizeLine((string) $data['email'], 190)),
            'segment' => $this->nullableLine((string) $data['segment'], 120),
            'subject' => $this->normalizeLine((string) $data['subject'], 190),
            'message' => $this->normalizeText((string) $data['message']),
            'origin_url' => $this->nullableLine((string) ($data['origin_url'] ?? ''), 255),
            'ip_address' => $this->nullableLine((string) ($data['ip_address'] ?? ''), 64),
            'user_agent' => $this->nullableLine((string) ($data['user_agent'] ?? ''), 255),
            'status' => self::DEFAULT_STATUS,
            'status_updated_at' => $submittedAt,
            'status_updated_by_member_id' => null,
            'status_updated_by_name' => null,
        ]);

        $requestId = (int) $this->pdo->lastInsertId();
        if ($requestId > 0) {
            $this->insertEvent($requestId, 'created', '', self::DEFAULT_STATUS, null, null, '');
        }
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
     *   user_agent: string,
     *   status: string,
     *   status_updated_at: string,
     *   status_updated_by_member_id: int|null,
     *   status_updated_by_name: string
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
                COALESCE(user_agent, '') AS user_agent,
                COALESCE(status, 'novo') AS status,
                COALESCE(
                    DATE_FORMAT(status_updated_at, '%Y-%m-%d %H:%i:%s'),
                    DATE_FORMAT(submitted_at, '%Y-%m-%d %H:%i:%s')
                ) AS status_updated_at,
                status_updated_by_member_id,
                COALESCE(status_updated_by_name, '') AS status_updated_by_name
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
            'status' => (string) ($row['status'] ?? self::DEFAULT_STATUS),
            'status_updated_at' => (string) ($row['status_updated_at'] ?? ''),
            'status_updated_by_member_id' => isset($row['status_updated_by_member_id']) && $row['status_updated_by_member_id'] !== null
                ? (int) $row['status_updated_by_member_id']
                : null,
            'status_updated_by_name' => (string) ($row['status_updated_by_name'] ?? ''),
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
                status VARCHAR(32) NOT NULL DEFAULT 'novo',
                status_updated_at DATETIME NULL,
                status_updated_by_member_id BIGINT UNSIGNED NULL,
                status_updated_by_name VARCHAR(160) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_contact_requests_protocol (request_protocol),
                UNIQUE KEY uq_contact_requests_request_id (request_id),
                KEY idx_contact_requests_submitted_at (submitted_at),
                KEY idx_contact_requests_status (status),
                KEY idx_contact_requests_email (email),
                KEY idx_contact_requests_subject (subject)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        $this->ensureColumn(
            'contact_requests',
            'status',
            "ALTER TABLE contact_requests ADD COLUMN status VARCHAR(32) NOT NULL DEFAULT 'novo' AFTER user_agent"
        );
        $this->ensureColumn(
            'contact_requests',
            'status_updated_at',
            'ALTER TABLE contact_requests ADD COLUMN status_updated_at DATETIME NULL AFTER status'
        );
        $this->ensureColumn(
            'contact_requests',
            'status_updated_by_member_id',
            'ALTER TABLE contact_requests ADD COLUMN status_updated_by_member_id BIGINT UNSIGNED NULL AFTER status_updated_at'
        );
        $this->ensureColumn(
            'contact_requests',
            'status_updated_by_name',
            'ALTER TABLE contact_requests ADD COLUMN status_updated_by_name VARCHAR(160) NULL AFTER status_updated_by_member_id'
        );

        $this->pdo->exec(
            "UPDATE contact_requests
             SET status = 'novo'
             WHERE status IS NULL OR TRIM(status) = ''"
        );
        $this->pdo->exec(
            'UPDATE contact_requests
             SET status_updated_at = submitted_at
             WHERE status_updated_at IS NULL'
        );

        $this->pdo->exec(<<<SQL
            CREATE TABLE IF NOT EXISTS contact_request_events (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                contact_request_id BIGINT UNSIGNED NOT NULL,
                event_type VARCHAR(40) NOT NULL,
                previous_status VARCHAR(32) NULL,
                next_status VARCHAR(32) NOT NULL,
                note VARCHAR(500) NULL,
                actor_member_id BIGINT UNSIGNED NULL,
                actor_name VARCHAR(160) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                KEY idx_contact_request_events_request (contact_request_id),
                KEY idx_contact_request_events_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);
    }

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
    private function fetchEventsForAdmin(array $requestIds): array
    {
        $placeholders = implode(', ', array_fill(0, count($requestIds), '?'));
        $statement = $this->pdo->prepare(<<<SQL
            SELECT
                id,
                contact_request_id,
                event_type,
                COALESCE(previous_status, '') AS previous_status,
                next_status,
                COALESCE(note, '') AS note,
                actor_member_id,
                COALESCE(actor_name, '') AS actor_name,
                DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS created_at
            FROM contact_request_events
            WHERE contact_request_id IN ($placeholders)
            ORDER BY created_at DESC, id DESC
        SQL);
        $statement->execute($requestIds);

        $rows = $statement->fetchAll() ?: [];
        $eventsByRequest = [];

        foreach ($rows as $row) {
            $contactRequestId = (int) ($row['contact_request_id'] ?? 0);
            if ($contactRequestId <= 0) {
                continue;
            }

            if (!isset($eventsByRequest[$contactRequestId])) {
                $eventsByRequest[$contactRequestId] = [];
            }

            $eventsByRequest[$contactRequestId][] = [
                'id' => (int) ($row['id'] ?? 0),
                'contact_request_id' => $contactRequestId,
                'event_type' => (string) ($row['event_type'] ?? ''),
                'previous_status' => (string) ($row['previous_status'] ?? ''),
                'next_status' => (string) ($row['next_status'] ?? ''),
                'note' => (string) ($row['note'] ?? ''),
                'actor_member_id' => isset($row['actor_member_id']) && $row['actor_member_id'] !== null
                    ? (int) $row['actor_member_id']
                    : null,
                'actor_name' => (string) ($row['actor_name'] ?? ''),
                'created_at' => (string) ($row['created_at'] ?? ''),
            ];
        }

        return $eventsByRequest;
    }

    private function persistStatusUpdate(
        int $requestId,
        string $status,
        ?int $actorMemberId,
        string $actorName,
        string $note
    ): bool {
        $normalizedStatus = $this->normalizeLine($status, 32);
        if ($normalizedStatus === '') {
            return false;
        }

        $selectStatement = $this->pdo->prepare(
            'SELECT status FROM contact_requests WHERE id = :id LIMIT 1'
        );
        $selectStatement->execute(['id' => $requestId]);
        $currentStatus = $selectStatement->fetchColumn();
        if (!is_string($currentStatus) || trim($currentStatus) === '') {
            return false;
        }

        $normalizedCurrentStatus = $this->normalizeLine($currentStatus, 32);
        $normalizedNote = $this->normalizeLine($note, 500);
        $normalizedActorName = $this->normalizeLine($actorName, 160);
        $actorId = $actorMemberId !== null && $actorMemberId > 0 ? $actorMemberId : null;
        $now = (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s');

        if ($normalizedCurrentStatus === $normalizedStatus && $normalizedNote === '') {
            return true;
        }

        $updateStatement = $this->pdo->prepare(
            'UPDATE contact_requests
             SET status = :status,
                 status_updated_at = :status_updated_at,
                 status_updated_by_member_id = :status_updated_by_member_id,
                 status_updated_by_name = :status_updated_by_name
             WHERE id = :id
             LIMIT 1'
        );
        $updated = $updateStatement->execute([
            'status' => $normalizedStatus,
            'status_updated_at' => $now,
            'status_updated_by_member_id' => $actorId,
            'status_updated_by_name' => $normalizedActorName !== '' ? $normalizedActorName : null,
            'id' => $requestId,
        ]);
        if (!$updated) {
            return false;
        }

        $this->insertEvent(
            $requestId,
            'status_changed',
            $normalizedCurrentStatus,
            $normalizedStatus,
            $actorId,
            $normalizedActorName !== '' ? $normalizedActorName : null,
            $normalizedNote
        );

        return true;
    }

    private function insertEvent(
        int $requestId,
        string $eventType,
        string $previousStatus,
        string $nextStatus,
        ?int $actorMemberId,
        ?string $actorName,
        string $note
    ): void {
        $statement = $this->pdo->prepare(<<<SQL
            INSERT INTO contact_request_events (
                contact_request_id,
                event_type,
                previous_status,
                next_status,
                note,
                actor_member_id,
                actor_name
            ) VALUES (
                :contact_request_id,
                :event_type,
                :previous_status,
                :next_status,
                :note,
                :actor_member_id,
                :actor_name
            )
        SQL);
        $statement->execute([
            'contact_request_id' => $requestId,
            'event_type' => $this->normalizeLine($eventType, 40),
            'previous_status' => $this->nullableLine($previousStatus, 32),
            'next_status' => $this->normalizeLine($nextStatus, 32),
            'note' => $this->nullableLine($note, 500),
            'actor_member_id' => $actorMemberId !== null && $actorMemberId > 0 ? $actorMemberId : null,
            'actor_name' => $actorName !== null && trim($actorName) !== ''
                ? $this->normalizeLine($actorName, 160)
                : null,
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

    private function ensureColumn(string $tableName, string $columnName, string $alterSql): void
    {
        $statement = $this->pdo->prepare(
            'SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS '
            . 'WHERE TABLE_SCHEMA = DATABASE() '
            . 'AND TABLE_NAME = :table_name '
            . 'AND COLUMN_NAME = :column_name'
        );
        $statement->execute([
            'table_name' => $tableName,
            'column_name' => $columnName,
        ]);

        $exists = (int) $statement->fetchColumn() > 0;

        if (!$exists) {
            $this->pdo->exec($alterSql);
        }
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
