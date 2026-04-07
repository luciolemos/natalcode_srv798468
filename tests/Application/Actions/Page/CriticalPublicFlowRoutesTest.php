<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Page;

use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

class CriticalPublicFlowRoutesTest extends TestCase
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

    public function testCriticalPublicPagesRenderOnGet(): void
    {
        foreach (['/cadastro', '/entrar', '/esqueci-senha', '/redefinir-senha', '/contato'] as $path) {
            $response = $this->dispatch('GET', $path);
            $this->assertSame(200, $response->getStatusCode(), 'GET ' . $path . ' should return 200.');
        }
    }

    public function testCadastroPostWithInvalidPayloadRedirectsBackToCadastro(): void
    {
        $response = $this->dispatch('POST', '/cadastro', [
            'full_name' => '',
            'email' => 'email-invalido',
            'password' => 'abc',
            'password_confirmation' => 'abcd',
        ]);

        $this->assertSame(303, $response->getStatusCode());
        $this->assertSame('/cadastro', $response->getHeaderLine('Location'));
    }

    public function testEntrarPostWithInvalidCredentialsRedirectsBackToEntrar(): void
    {
        $response = $this->dispatch('POST', '/entrar', [
            'identifier' => 'nao.existe@natalcode.com',
            'password' => 'SenhaInvalida123',
        ]);

        $this->assertSame(303, $response->getStatusCode());
        $this->assertSame('/entrar', $response->getHeaderLine('Location'));
    }

    public function testEsqueciSenhaPostWithInvalidEmailRedirectsBackToEsqueciSenha(): void
    {
        $response = $this->dispatch('POST', '/esqueci-senha', [
            'email' => 'nao-e-email',
        ]);

        $this->assertSame(303, $response->getStatusCode());
        $this->assertSame('/esqueci-senha', $response->getHeaderLine('Location'));
    }

    public function testContatoPostWithInvalidPayloadRedirectsBackToContato(): void
    {
        $response = $this->dispatch('POST', '/contato', [
            'name' => '',
            'email' => 'invalido',
            'subject' => '',
            'message' => 'curta',
        ]);

        $this->assertSame(303, $response->getStatusCode());
        $this->assertSame('/contato', $response->getHeaderLine('Location'));
    }

    public function testContatoPostWithHoneypotFilledRedirectsBackToContato(): void
    {
        $response = $this->dispatch('POST', '/contato', [
            'name' => '',
            'email' => '',
            'subject' => '',
            'message' => '',
            'company' => 'bot',
        ]);

        $this->assertSame(303, $response->getStatusCode());
        $this->assertSame('/contato', $response->getHeaderLine('Location'));
    }

    /**
     * @param array<string, mixed> $parsedBody
     */
    private function dispatch(string $method, string $path, array $parsedBody = []): ResponseInterface
    {
        $app = $this->getAppInstance();
        $request = $this->createRequest($method, $path, ['HTTP_ACCEPT' => 'text/html']);

        if ($parsedBody !== []) {
            $request = $request->withParsedBody($parsedBody);
        }

        return $app->handle($request);
    }

    private function backupEnv(): void
    {
        foreach ($this->getManagedEnvKeys() as $key) {
            $this->originalEnv[$key] = array_key_exists($key, $_ENV) ? (string) $_ENV[$key] : null;
        }
    }

    private function restoreEnv(): void
    {
        foreach ($this->getManagedEnvKeys() as $key) {
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
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        $_SESSION = [];
    }

    /**
     * @return list<string>
     */
    private function getManagedEnvKeys(): array
    {
        return [
            'RECAPTCHA_ENABLED',
        ];
    }
}
