<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityTest extends TestCase
{
    public function test_security_headers_are_sent(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        $csp = (string) $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("script-src 'self' https://www.googletagmanager.com", $csp);
        $this->assertStringNotContainsString('unsafe-inline', $csp);
    }

    public function test_non_production_responses_are_noindex(): void
    {
        $this->get('/')->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
    }

    public function test_production_responses_are_indexable(): void
    {
        $this->app['env'] = 'production';

        $this->get('/')->assertHeaderMissing('X-Robots-Tag');
    }

    public function test_access_guard_requires_credentials_when_configured(): void
    {
        config(['app.access_guard.user' => 'dev', 'app.access_guard.pass' => 's3nha:com:dois-pontos']);

        $this->get('/')->assertUnauthorized()->assertHeader('WWW-Authenticate');
        $this->get('/', ['PHP_AUTH_USER' => 'dev', 'PHP_AUTH_PW' => 'errada'])->assertUnauthorized();
        $this->withServerVariables(['PHP_AUTH_USER' => 'dev', 'PHP_AUTH_PW' => 's3nha:com:dois-pontos'])->get('/')->assertOk();
        $this->withServerVariables([])->get('/up')->assertOk();
    }
}
