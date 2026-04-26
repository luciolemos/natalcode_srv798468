<?php

declare(strict_types=1);

namespace Tests\Application\Smoke;

use Tests\TestCase;

final class HealthRoutesTest extends TestCase
{
    /** @var array<string, string|null> */
    private array $originalEnv = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->backupEnv();
    }

    protected function tearDown(): void
    {
        $this->restoreEnv();

        parent::tearDown();
    }

    public function testHealthRenderRouteReturnsOk(): void
    {
        $app = $this->getAppInstance();
        $request = $this->createRequest('GET', '/health/render', ['HTTP_ACCEPT' => 'text/plain']);
        $response = $app->handle($request);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testHealthDbRouteReturnsServiceUnavailableWhenNotConfigured(): void
    {
        $app = $this->getAppInstance();
        $request = $this->createRequest('GET', '/health/db', ['HTTP_ACCEPT' => 'text/plain']);
        $response = $app->handle($request);

        $this->assertSame(503, $response->getStatusCode());
    }

    public function testHealthEndpointsAreNotExposedInProductionWithoutTokenOrAllowedIp(): void
    {
        $_ENV['APP_ENV'] = 'production';
        $_ENV['APP_HEALTH_TOKEN'] = '';
        $_ENV['APP_HEALTH_ALLOWED_IPS'] = '';

        $app = $this->getAppInstance();
        $request = $this->createRequest('GET', '/health/render', ['HTTP_ACCEPT' => 'application/json']);
        $response = $app->handle($request);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testHealthDbRouteAllowsProductionAccessWithValidToken(): void
    {
        $_ENV['APP_ENV'] = 'production';
        $_ENV['APP_HEALTH_TOKEN'] = 'health-test-token';
        $_ENV['APP_HEALTH_ALLOWED_IPS'] = '';

        $app = $this->getAppInstance();
        $request = $this->createRequest('GET', '/health/db', [
            'HTTP_ACCEPT' => 'application/json',
            'X-Health-Token' => 'health-test-token',
        ]);
        $response = $app->handle($request);

        $this->assertSame(503, $response->getStatusCode());
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
            'APP_ENV',
            'APP_HEALTH_TOKEN',
            'APP_HEALTH_ALLOWED_IPS',
        ];
    }
}
