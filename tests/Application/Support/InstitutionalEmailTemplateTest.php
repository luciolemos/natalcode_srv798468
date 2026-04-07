<?php

declare(strict_types=1);

namespace Tests\Application\Support;

use App\Application\Support\InstitutionalEmailTemplate;
use Tests\TestCase;

class InstitutionalEmailTemplateTest extends TestCase
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

    public function testBuildInstitutionHeaderMetaUsesEnvFallbackAndEscapesValues(): void
    {
        $_ENV['APP_DEFAULT_CNPJ'] = '12.345.678/0001-90';

        $html = InstitutionalEmailTemplate::buildInstitutionHeaderMeta('Natal & Code', null);

        $this->assertStringContainsString('Natal &amp; Code', $html);
        $this->assertStringContainsString('CNPJ: 12.345.678/0001-90', $html);
    }

    public function testBuildLayoutUsesConfiguredSiteDataAndEscapesTitle(): void
    {
        $_ENV['APP_DEFAULT_SITE_NAME'] = 'Natal & Code';
        $_ENV['APP_DEFAULT_PAGE_URL'] = 'https://example.org/';
        $_ENV['MAIL_PUBLIC_EMAIL'] = 'contato@example.org';

        $html = InstitutionalEmailTemplate::buildLayout(
            'Aviso <Importante>',
            '<p>Conteudo</p>',
            null,
            '<p>Cabecalho</p>'
        );

        $this->assertStringContainsString('Aviso &lt;Importante&gt;', $html);
        $this->assertStringContainsString('Natal &amp; Code', $html);
        $this->assertStringContainsString('mailto:contato@example.org', $html);
        $this->assertStringContainsString('https://example.org/assets/img/brand/natalcode1.png', $html);
        $this->assertStringContainsString('<p>Cabecalho</p>', $html);
    }

    public function testBuildActionGroupRendersOnlyValidActions(): void
    {
        $html = InstitutionalEmailTemplate::buildActionGroup([
            [
                'href' => 'https://example.org/aprovar?token=abc&next=/painel',
                'label' => 'Aprovar',
                'is_primary' => true,
            ],
            [
                'href' => 'https://example.org/ver',
                'label' => 'Ver detalhes',
            ],
            [
                'href' => '',
                'label' => 'Invalido',
            ],
        ]);

        $this->assertStringContainsString('Aprovar', $html);
        $this->assertStringContainsString('https://example.org/aprovar?token=abc&amp;next=/painel', $html);
        $this->assertStringContainsString('background:#2563eb', $html);
        $this->assertStringContainsString('Ver detalhes', $html);
        $this->assertStringNotContainsString('Invalido', $html);
    }

    /**
     * @return list<string>
     */
    private function getManagedEnvKeys(): array
    {
        return [
            'APP_DEFAULT_CNPJ',
            'APP_DEFAULT_SITE_NAME',
            'APP_DEFAULT_PAGE_URL',
            'MAIL_PUBLIC_EMAIL',
        ];
    }
}
