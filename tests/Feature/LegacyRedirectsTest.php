<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LegacyRedirectsTest extends TestCase
{
    public static function redirects(): array
    {
        return [
            ['/quem-somos', '/sobre'],
            ['/quem-somos/estatuto', '/sobre'],
            ['/estudos', '/servicos'],
            ['/estudos/esde', '/servicos/landing-pages'],
            ['/estudos/eade', '/servicos/sites-institucionais'],
            ['/estudos/palestras', '/servicos/seo-e-conteudo'],
            ['/estudos/atendimento-fraterno', '/servicos/suporte-e-manutencao'],
            ['/faq/doutrina', '/faq'],
            ['/agenda', '/'],
            ['/agenda/esde-i', '/'],
            ['/agenda/esde-i/ics', '/'],
            ['/loja/livraria', '/portfolio'],
            ['/cadastro', '/contato'],
        ];
    }

    #[DataProvider('redirects')]
    public function test_old_slim_url_redirects_permanently(string $from, string $to): void
    {
        $this->get($from)->assertStatus(301)->assertRedirect($to);
    }

    public function test_every_redirect_target_exists(): void
    {
        foreach (array_unique(config('redirects')) as $target) {
            $this->get($target)->assertOk();
        }
    }
}
