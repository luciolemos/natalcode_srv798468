<?php

declare(strict_types=1);

namespace Tests\Application\Routes;

use Tests\TestCase;

final class EventIngestionRouteTest extends TestCase
{
    /** @var array<string, string|null> */
    private array $originalEnv = [];
    private string $eventLogPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->backupEnv();
        $this->eventLogPath = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . '/natalcode-events-test.log';
        $_ENV['APP_EVENT_LOG'] = $this->eventLogPath;
        @unlink($this->eventLogPath);
    }

    protected function tearDown(): void
    {
        @unlink($this->eventLogPath);
        $this->restoreEnv();

        parent::tearDown();
    }

    public function testEventsRouteAcceptsValidPayload(): void
    {
        $_ENV['APP_EVENT_MAX_PAYLOAD_BYTES'] = '8192';

        $app = $this->getAppInstance();
        $request = $this->createRequest('POST', '/events', [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'text/plain',
        ]);
        $request->getBody()->write((string) json_encode([
            'type' => 'page_view',
            'path' => '/portfolio',
            'title' => 'Portfolio',
        ]));
        $request->getBody()->rewind();

        $response = $app->handle($request);

        $this->assertSame(204, $response->getStatusCode());
    }

    public function testEventsRouteRejectsUnknownEventType(): void
    {
        $_ENV['APP_EVENT_MAX_PAYLOAD_BYTES'] = '8192';

        $app = $this->getAppInstance();
        $request = $this->createRequest('POST', '/events', [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'text/plain',
        ]);
        $request->getBody()->write((string) json_encode([
            'type' => 'drop_table',
            'path' => '/portfolio',
        ]));
        $request->getBody()->rewind();

        $response = $app->handle($request);

        $this->assertSame(400, $response->getStatusCode());
    }

    public function testEventsRouteRejectsOversizedPayload(): void
    {
        $_ENV['APP_EVENT_MAX_PAYLOAD_BYTES'] = '600';

        $app = $this->getAppInstance();
        $request = $this->createRequest('POST', '/events', [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'text/plain',
        ]);
        $request->getBody()->write((string) json_encode([
            'type' => 'page_view',
            'title' => str_repeat('x', 2000),
        ]));
        $request->getBody()->rewind();

        $response = $app->handle($request);

        $this->assertSame(413, $response->getStatusCode());
    }

    private function backupEnv(): void
    {
        foreach ($this->managedEnvKeys() as $key) {
            $this->originalEnv[$key] = array_key_exists($key, $_ENV) ? (string) $_ENV[$key] : null;
        }
    }

    private function restoreEnv(): void
    {
        foreach ($this->managedEnvKeys() as $key) {
            $originalValue = $this->originalEnv[$key] ?? null;

            if ($originalValue === null) {
                unset($_ENV[$key]);
                continue;
            }

            $_ENV[$key] = $originalValue;
        }
    }

    /**
     * @return list<string>
     */
    private function managedEnvKeys(): array
    {
        return [
            'APP_EVENT_MAX_PAYLOAD_BYTES',
            'APP_EVENT_LOG',
        ];
    }
}
