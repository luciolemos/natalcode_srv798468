<?php

declare(strict_types=1);

namespace Tests\Application\Routes;

use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

final class AdminPanelAccessFlowTest extends TestCase
{
    /** @var array<string, string|null> */
    private array $originalEnv = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->backupEnv();
        $_ENV['RECAPTCHA_ENABLED'] = 'false';
        $this->resetSessionState();
    }

    protected function tearDown(): void
    {
        $this->restoreEnv();
        $this->resetSessionState();

        parent::tearDown();
    }

    public function testPainelRedirectsToEntrarWhenUserIsNotAuthenticated(): void
    {
        $response = $this->dispatch('GET', '/painel');

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/entrar', $response->getHeaderLine('Location'));
    }

    public function testOperatorCanAccessPainelDashboard(): void
    {
        $_SESSION['member_authenticated'] = true;
        $_SESSION['member_role_key'] = 'operator';
        $_SESSION['member_role_name'] = 'Operador';
        $_SESSION['member_name'] = 'Usuário Operador';

        $response = $this->dispatch('GET', '/painel');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testOperatorCannotAccessAdminOnlyUsersPage(): void
    {
        $_SESSION['member_authenticated'] = true;
        $_SESSION['member_role_key'] = 'operator';
        $_SESSION['member_role_name'] = 'Operador';

        $response = $this->dispatch('GET', '/painel/usuarios');

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/membro?status=forbidden', $response->getHeaderLine('Location'));
    }

    public function testLegacyAdminLoginPathRedirectsToPainelLogin(): void
    {
        $response = $this->dispatch('GET', '/admin/login');

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/painel/login', $response->getHeaderLine('Location'));
    }

    private function dispatch(string $method, string $path): ResponseInterface
    {
        $app = $this->getAppInstance();
        $request = $this->createRequest($method, $path, ['HTTP_ACCEPT' => 'text/html']);

        return $app->handle($request);
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

    private function resetSessionState(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION) || !is_array($_SESSION)) {
            $_SESSION = [];
        }

        $_SESSION = [];
    }

    /**
     * @return list<string>
     */
    private function managedEnvKeys(): array
    {
        return [
            'RECAPTCHA_ENABLED',
        ];
    }
}
