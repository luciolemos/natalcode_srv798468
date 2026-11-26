<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    public static function pages(): array
    {
        return [
            'início' => ['/', 'Software, sites e produtos digitais que convertem'],
            'serviços' => ['/servicos', 'Soluções digitais para cada fase do seu negócio'],
            'landing pages' => ['/servicos/landing-pages', 'Landing pages'],
            'sites institucionais' => ['/servicos/sites-institucionais', 'Sites institucionais'],
            'sistemas web' => ['/servicos/sistemas-web', 'Sistemas web'],
            'seo' => ['/servicos/seo-e-conteudo', 'SEO e conteúdo'],
            'suporte' => ['/servicos/suporte-e-manutencao', 'Suporte e manutenção'],
            'portfólio' => ['/portfolio', 'Projetos e modelos por segmento'],
            'sobre' => ['/sobre', 'Design e desenvolvimento a serviço do seu negócio'],
            'dúvidas' => ['/faq', 'Perguntas frequentes'],
            'contato' => ['/contato', 'Vamos conversar sobre o seu projeto'],
            'privacidade' => ['/politica-de-privacidade', 'Política de Privacidade'],
        ];
    }

    #[DataProvider('pages')]
    public function test_public_page_renders_with_seo_tags(string $path, string $heading): void
    {
        $response = $this->get($path);

        $response->assertOk()
            ->assertSee($heading)
            ->assertSee('<link rel="canonical" href="'.url($path).'">', false)
            ->assertSee('property="og:title"', false)
            ->assertSee('application/ld+json', false);
    }

    public function test_unknown_service_returns_404_page(): void
    {
        $this->get('/servicos/nao-existe')->assertNotFound()->assertSee('Página não encontrada');
    }

    public function test_home_has_no_testimonials_section_while_list_is_empty(): void
    {
        config(['site.testimonials' => []]);

        $this->get('/')->assertDontSee('Quem já trabalhou com a gente');
    }

    public function test_home_shows_testimonials_when_configured(): void
    {
        config(['site.testimonials' => [['quote' => 'Ótimo trabalho', 'name' => 'Cliente Real', 'role' => 'Diretora']]]);

        $this->get('/')->assertSee('Quem já trabalhou com a gente')->assertSee('Cliente Real');
    }

    public function test_contact_page_prefills_whatsapp_message_with_subject(): void
    {
        $this->get('/contato?assunto=Landing+page')
            ->assertOk()
            ->assertSee(rawurlencode('quero conversar sobre: Landing page.'), false);
    }

    public function test_faq_page_exposes_faq_structured_data(): void
    {
        $this->get('/faq')->assertSee('"@type":"FAQPage"', false);
    }

    public function test_sitemap_lists_public_pages(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        foreach (['/', '/servicos', '/servicos/sistemas-web', '/portfolio', '/sobre', '/faq', '/contato', '/politica-de-privacidade'] as $path) {
            $response->assertSee('<loc>'.url($path).'</loc>', false);
        }
    }

    public function test_analytics_and_consent_banner_only_render_with_measurement_id(): void
    {
        config(['site.analytics.ga4' => null]);
        $this->get('/')->assertDontSee('data-ga4-id', false)->assertDontSee('data-consent', false);

        config(['site.analytics.ga4' => 'G-TESTE123']);
        $this->get('/')->assertSee('data-ga4-id="G-TESTE123"', false)->assertSee('data-consent-choice="granted"', false);
    }

    public function test_footer_and_consent_banner_link_to_privacy_policy(): void
    {
        config(['site.analytics.ga4' => 'G-TESTE123']);

        $link = 'href="'.url('/politica-de-privacidade').'"';
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(2, substr_count($html, $link), 'Rodapé e banner de cookies devem apontar para a política.');
    }
}
