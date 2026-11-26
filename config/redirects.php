<?php

/*
|--------------------------------------------------------------------------
| Redirecionamentos permanentes (301) das URLs do site antigo (Slim)
|--------------------------------------------------------------------------
|
| Preservam links externos e posições no Google na troca de versão.
| Chave: caminho antigo. Valor: caminho novo.
|
*/

return [
    // Quem somos -> Sobre
    '/quem-somos' => '/sobre',
    '/quem-somos/missao' => '/sobre',
    '/quem-somos/valores' => '/sobre',
    '/quem-somos/historia' => '/sobre',
    '/quem-somos/fundador' => '/sobre',
    '/quem-somos/nossa-marca' => '/sobre',
    '/quem-somos/equipe' => '/sobre',
    '/quem-somos/gestao-cede' => '/sobre',
    '/quem-somos/estatuto' => '/sobre',
    '/quem-somos/base-de-conhecimento' => '/sobre',

    // Serviços (slugs herdados do projeto CEDE)
    '/estudos' => '/servicos',
    '/estudos/esde' => '/servicos/landing-pages',
    '/estudos/eade' => '/servicos/sites-institucionais',
    '/estudos/palestras' => '/servicos/seo-e-conteudo',
    '/estudos/atendimento-fraterno' => '/servicos/suporte-e-manutencao',

    // Dúvidas por categoria -> página única
    '/faq/doutrina' => '/faq',
    '/faq/participacao' => '/faq',
    '/faq/praticas' => '/faq',

    // Conteúdo do CEDE retirado do site
    '/agenda' => '/',
    '/loja' => '/portfolio',
    '/loja/bazar' => '/portfolio',
    '/loja/livraria' => '/portfolio',
    '/loja/livraria-ii' => '/portfolio',
    '/loja/livraria-auta-de-sousa' => '/portfolio',
    '/loja/biblioteca' => '/sobre',
    '/livraria' => '/portfolio',
    '/livraria-ii' => '/portfolio',
    '/livraria-auta-de-sousa' => '/portfolio',
    '/biblioteca' => '/sobre',
    '/cadastro' => '/contato',
];
