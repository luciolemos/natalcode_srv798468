<?php

declare(strict_types=1);

namespace Tests\Application\Smoke;

use Tests\TestCase;

final class HealthRoutesTest extends TestCase
{
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
}
