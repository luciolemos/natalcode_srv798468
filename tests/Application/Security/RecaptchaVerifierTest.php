<?php

declare(strict_types=1);

namespace Tests\Application\Security;

use App\Application\Security\RecaptchaVerifier;
use Tests\TestCase;

class RecaptchaVerifierTest extends TestCase
{
    /** @var array<string, string|null> */
    private array $originalEnv = [];

    protected function setUp(): void
    {
        parent::setUp();

        foreach ($this->getManagedEnvKeys() as $key) {
            $this->originalEnv[$key] = array_key_exists($key, $_ENV) ? (string) $_ENV[$key] : null;
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->getManagedEnvKeys() as $key) {
            $originalValue = $this->originalEnv[$key] ?? null;

            if ($originalValue === null) {
                unset($_ENV[$key]);
                continue;
            }

            $_ENV[$key] = $originalValue;
        }

        parent::tearDown();
    }

    public function testVerifierAllowsSubmissionWhenRecaptchaIsDisabled(): void
    {
        $_ENV['RECAPTCHA_ENABLED'] = 'false';
        unset($_ENV['RECAPTCHA_SITE_KEY'], $_ENV['RECAPTCHA_SECRET_KEY'], $_ENV['RECAPTCHA_ALLOWED_HOSTNAME']);

        $verifier = new RecaptchaVerifier();
        $result = $verifier->verifySubmission('', 'contact_submit', 'cedern.org', '127.0.0.1');

        $this->assertTrue($result['ok']);
        $this->assertSame('', $result['message']);
        $this->assertSame([], $result['error_codes']);
    }

    public function testVerifierRejectsMissingTokenWhenEnabled(): void
    {
        $_ENV['RECAPTCHA_ENABLED'] = 'true';
        $_ENV['RECAPTCHA_SITE_KEY'] = 'site-key';
        $_ENV['RECAPTCHA_SECRET_KEY'] = 'secret-key';
        $_ENV['RECAPTCHA_ALLOWED_HOSTNAME'] = 'cedern.org';

        $verifier = new RecaptchaVerifier();
        $result = $verifier->verifySubmission('', 'contact_submit', 'cedern.org', '127.0.0.1');

        $this->assertFalse($result['ok']);
        $this->assertSame('Confirme a validacao anti-spam e tente novamente.', $result['message']);
    }

    public function testVerifierAcceptsValidResponse(): void
    {
        $_ENV['RECAPTCHA_ENABLED'] = 'true';
        $_ENV['RECAPTCHA_SITE_KEY'] = 'site-key';
        $_ENV['RECAPTCHA_SECRET_KEY'] = 'secret-key';
        $_ENV['RECAPTCHA_MIN_SCORE'] = '0.5';
        $_ENV['RECAPTCHA_ALLOWED_HOSTNAME'] = 'cedern.org';

        $verifier = new class () extends RecaptchaVerifier {
            /**
             * @param array<string, string> $payload
             * @return array<string, mixed>
             */
            protected function performVerificationRequest(array $payload): array
            {
                return [
                    'success' => true,
                    'score' => 0.9,
                    'action' => 'contact_submit',
                    'hostname' => 'cedern.org',
                ];
            }
        };

        $result = $verifier->verifySubmission('token-value', 'contact_submit', 'cedern.org', '127.0.0.1');

        $this->assertTrue($result['ok']);
        $this->assertSame(0.9, $result['score']);
    }

    public function testVerifierRejectsLowScore(): void
    {
        $_ENV['RECAPTCHA_ENABLED'] = 'true';
        $_ENV['RECAPTCHA_SITE_KEY'] = 'site-key';
        $_ENV['RECAPTCHA_SECRET_KEY'] = 'secret-key';
        $_ENV['RECAPTCHA_MIN_SCORE'] = '0.5';
        $_ENV['RECAPTCHA_ALLOWED_HOSTNAME'] = 'cedern.org';

        $verifier = new class () extends RecaptchaVerifier {
            /**
             * @param array<string, string> $payload
             * @return array<string, mixed>
             */
            protected function performVerificationRequest(array $payload): array
            {
                return [
                    'success' => true,
                    'score' => 0.2,
                    'action' => 'member_login',
                    'hostname' => 'cedern.org',
                ];
            }
        };

        $result = $verifier->verifySubmission('token-value', 'member_login', 'cedern.org', '127.0.0.1');

        $this->assertFalse($result['ok']);
        $this->assertSame(
            'Sua solicitacao nao passou na verificacao de seguranca. Tente novamente.',
            $result['message']
        );
    }

    /**
     * @return list<string>
     */
    private function getManagedEnvKeys(): array
    {
        return [
            'RECAPTCHA_ENABLED',
            'RECAPTCHA_SITE_KEY',
            'RECAPTCHA_SECRET_KEY',
            'RECAPTCHA_MIN_SCORE',
            'RECAPTCHA_ALLOWED_HOSTNAME',
        ];
    }
}
