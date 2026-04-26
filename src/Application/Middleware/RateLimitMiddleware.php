<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class RateLimitMiddleware implements Middleware
{
    private const DEFAULT_STORAGE_DIR = 'var/cache/rate-limit';

    /**
     * @var array<int, array{
     *   name: string,
     *   method: string,
     *   path: string,
     *   max: int,
     *   window: int,
     *   key: string,
     *   identity_fields?: array<int, string>
     * }>
     */
    private const RULES = [
        [
            'name' => 'member_login_ip',
            'method' => 'POST',
            'path' => '/entrar',
            'max' => 20,
            'window' => 900,
            'key' => 'ip',
        ],
        [
            'name' => 'member_login_identity',
            'method' => 'POST',
            'path' => '/entrar',
            'max' => 8,
            'window' => 900,
            'key' => 'ip+identity',
            'identity_fields' => ['identifier', 'email'],
        ],
        [
            'name' => 'member_register_ip',
            'method' => 'POST',
            'path' => '/cadastro',
            'max' => 8,
            'window' => 3600,
            'key' => 'ip',
        ],
        [
            'name' => 'member_password_forgot_ip',
            'method' => 'POST',
            'path' => '/esqueci-senha',
            'max' => 8,
            'window' => 3600,
            'key' => 'ip',
        ],
        [
            'name' => 'contact_submit_ip',
            'method' => 'POST',
            'path' => '/contato',
            'max' => 20,
            'window' => 3600,
            'key' => 'ip',
        ],
        [
            'name' => 'events_ingest_ip',
            'method' => 'POST',
            'path' => '/events',
            'max' => 300,
            'window' => 600,
            'key' => 'ip',
        ],
    ];

    public function process(Request $request, RequestHandler $handler): Response
    {
        if (!$this->isEnabled()) {
            return $handler->handle($request);
        }

        $method = strtoupper(trim($request->getMethod()));
        $path = $this->normalizePath($request->getUri()->getPath());
        $matchedRules = $this->resolveMatchedRules($method, $path);

        if ($matchedRules === []) {
            return $handler->handle($request);
        }

        foreach ($matchedRules as $rule) {
            $checkResult = $this->consumeAllowance($request, $rule, $path);

            if ($checkResult['allowed']) {
                continue;
            }

            return $this->buildRateLimitedResponse((int) $checkResult['retry_after'], (string) $rule['name']);
        }

        return $handler->handle($request);
    }

    private function isEnabled(): bool
    {
        $rawFlag = trim((string) ($_ENV['APP_RATE_LIMIT_ENABLED'] ?? ''));
        if ($rawFlag !== '') {
            return filter_var($rawFlag, FILTER_VALIDATE_BOOLEAN);
        }

        $appEnv = strtolower(trim((string) ($_ENV['APP_ENV'] ?? 'production')));

        return $appEnv !== 'test';
    }

    /**
     * @return array<int, array{
     *   name: string,
     *   method: string,
     *   path: string,
     *   max: int,
     *   window: int,
     *   key: string,
     *   identity_fields?: array<int, string>
     * }>
     */
    private function resolveMatchedRules(string $method, string $path): array
    {
        return array_values(array_filter(
            self::RULES,
            static fn (array $rule): bool => $rule['method'] === $method && $rule['path'] === $path
        ));
    }

    private function normalizePath(string $path): string
    {
        $normalized = '/' . ltrim(trim($path), '/');

        return $normalized === '/' ? '/' : rtrim($normalized, '/');
    }

    /**
     * @param array{
     *   name: string,
     *   method: string,
     *   path: string,
     *   max: int,
     *   window: int,
     *   key: string,
     *   identity_fields?: array<int, string>
     * } $rule
     * @return array{allowed: bool, retry_after: int}
     */
    private function consumeAllowance(Request $request, array $rule, string $path): array
    {
        $storageFile = $this->resolveStorageFilePath(
            (string) $rule['name'],
            $path,
            $this->resolveClientKey($request, $rule)
        );

        $storageDirectory = dirname($storageFile);

        if (!is_dir($storageDirectory) && !@mkdir($storageDirectory, 0775, true) && !is_dir($storageDirectory)) {
            return ['allowed' => true, 'retry_after' => 0];
        }

        $handle = @fopen($storageFile, 'c+');
        if ($handle === false) {
            return ['allowed' => true, 'retry_after' => 0];
        }

        if (!flock($handle, LOCK_EX)) {
            fclose($handle);

            return ['allowed' => true, 'retry_after' => 0];
        }

        $currentState = $this->readStateFromHandle($handle);
        $windowStart = (int) ($currentState['window_start'] ?? 0);
        $count = (int) ($currentState['count'] ?? 0);
        $windowSize = max(1, (int) $rule['window']);
        $maxRequests = max(1, (int) $rule['max']);
        $now = time();

        if ($windowStart <= 0 || ($now - $windowStart) >= $windowSize) {
            $windowStart = $now;
            $count = 0;
        }

        if ($count >= $maxRequests) {
            $retryAfter = $windowSize - ($now - $windowStart);
            if ($retryAfter < 1) {
                $retryAfter = 1;
            }

            flock($handle, LOCK_UN);
            fclose($handle);

            return ['allowed' => false, 'retry_after' => $retryAfter];
        }

        $count++;

        $nextState = [
            'window_start' => $windowStart,
            'count' => $count,
        ];

        $this->writeStateToHandle($handle, $nextState);

        flock($handle, LOCK_UN);
        fclose($handle);

        return ['allowed' => true, 'retry_after' => 0];
    }

    /**
     * @return array{window_start?: int, count?: int}
     */
    private function readStateFromHandle(mixed $handle): array
    {
        rewind($handle);

        $rawContents = stream_get_contents($handle);
        if (!is_string($rawContents) || trim($rawContents) === '') {
            return [];
        }

        /** @var mixed $decoded */
        $decoded = json_decode($rawContents, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param array{window_start: int, count: int} $state
     */
    private function writeStateToHandle(mixed $handle, array $state): void
    {
        rewind($handle);
        ftruncate($handle, 0);
        fwrite($handle, (string) json_encode($state, JSON_UNESCAPED_SLASHES));
        fflush($handle);
    }

    /**
     * @param array{
     *   name: string,
     *   method: string,
     *   path: string,
     *   max: int,
     *   window: int,
     *   key: string,
     *   identity_fields?: array<int, string>
     * } $rule
     */
    private function resolveClientKey(Request $request, array $rule): string
    {
        $ip = $this->resolveClientIp($request);

        if ($rule['key'] !== 'ip+identity') {
            return $ip;
        }

        $parsedBody = $request->getParsedBody();
        $identity = '';

        if (is_array($parsedBody)) {
            foreach ((array) ($rule['identity_fields'] ?? []) as $field) {
                $candidate = $this->normalizeLine((string) ($parsedBody[$field] ?? ''), 190);
                if ($candidate !== '') {
                    $identity = strtolower($candidate);
                    break;
                }
            }
        }

        if ($identity === '') {
            $identity = 'anonymous';
        }

        return $ip . '|' . $identity;
    }

    private function resolveClientIp(Request $request): string
    {
        $forwardedFor = trim((string) $request->getHeaderLine('X-Forwarded-For'));
        if ($forwardedFor !== '') {
            $candidates = array_map('trim', explode(',', $forwardedFor));

            foreach ($candidates as $candidate) {
                if (filter_var($candidate, FILTER_VALIDATE_IP) !== false) {
                    return $candidate;
                }
            }
        }

        $remoteAddress = trim((string) ($request->getServerParams()['REMOTE_ADDR'] ?? ''));

        if (filter_var($remoteAddress, FILTER_VALIDATE_IP) !== false) {
            return $remoteAddress;
        }

        return 'unknown';
    }

    private function normalizeLine(string $value, int $maxLength): string
    {
        $normalized = trim($value);
        if ($normalized === '') {
            return '';
        }

        $normalized = (string) preg_replace('/[\x00-\x1F\x7F]/', '', $normalized);
        if ($normalized === '') {
            return '';
        }

        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            return mb_strlen($normalized) > $maxLength
                ? trim((string) mb_substr($normalized, 0, $maxLength))
                : $normalized;
        }

        return strlen($normalized) > $maxLength
            ? trim(substr($normalized, 0, $maxLength))
            : $normalized;
    }

    private function resolveStorageFilePath(string $ruleName, string $path, string $clientKey): string
    {
        $storageDirectory = $this->resolveStorageDirectory();
        $hash = hash('sha256', $ruleName . '|' . $path . '|' . $clientKey);
        $nestedDirectory = $storageDirectory . '/' . substr($hash, 0, 2);

        return $nestedDirectory . '/' . $hash . '.json';
    }

    private function resolveStorageDirectory(): string
    {
        $configuredDirectory = trim((string) ($_ENV['APP_RATE_LIMIT_STORAGE_DIR'] ?? ''));
        $normalizedDirectory = $configuredDirectory !== ''
            ? $configuredDirectory
            : self::DEFAULT_STORAGE_DIR;

        $normalizedDirectory = str_replace('\\', '/', $normalizedDirectory);

        if (str_starts_with($normalizedDirectory, '/')) {
            return rtrim($normalizedDirectory, '/');
        }

        return dirname(__DIR__, 3) . '/' . ltrim($normalizedDirectory, '/');
    }

    private function buildRateLimitedResponse(int $retryAfter, string $ruleName): Response
    {
        $payload = [
            'status' => 'rate_limited',
            'message' => 'Muitas tentativas em pouco tempo. Aguarde e tente novamente.',
            'retry_after_seconds' => $retryAfter,
            'rule' => $ruleName,
        ];

        $response = new SlimResponse(429);
        $response->getBody()->write((string) json_encode($payload, JSON_UNESCAPED_UNICODE));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Retry-After', (string) $retryAfter);
    }
}
