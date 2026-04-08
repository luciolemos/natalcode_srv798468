<?php

declare(strict_types=1);

return [
    'labels' => [
        'inicio' => 'Início',
        'quem-somos' => 'Quem Somos',
        'missao' => 'Missao',
        'valores' => 'Valores',
        'historia' => 'Historia',
        'fundador' => 'Liderança',
        'estatuto' => 'Diretrizes',
        'nossa-marca' => 'Nossa Marca',
        'gestao-cede' => 'Gestão',
        'estudos' => 'Serviços',
        'esde' => 'Landing Pages',
        'eade' => 'Sites Institucionais',
        'palestras' => 'SEO e Conteúdo',
        'atendimento-fraterno' => 'Suporte e Manutenção',
        'agenda' => 'Processo',
        'estudo-do-evangelho' => 'Descoberta',
        'palestra-publica' => 'Implementação',
        'juventude-espirita' => 'Otimização',
        'loja' => 'Recursos',
        'bazar' => 'Materiais',
        'livraria' => 'NatalCode Labs',
        'livraria-ii' => 'NatalCode Labs',
        'livraria-auta-de-sousa' => 'NatalCode Labs',
        'faq' => 'FAQ',
        'biblioteca' => 'Base de conhecimento',
        'base-de-conhecimento' => 'Base de conhecimento',
        'doutrina' => 'Estratégia',
        'participacao' => 'Contratação',
        'praticas' => 'Entrega',
        'politica-de-privacidade' => 'Política de Privacidade',
        'dados-de-acesso' => 'Dados de acesso',
        'termos-de-uso' => 'Termos de Uso',
        'contato' => 'Contato',
    ],
    'menu' => [
        [
            'key' => 'quem-somos',
            'base' => '/quem-somos',
            'items' => [
                ['path' => '/quem-somos', 'label' => 'Visão geral'],
                ['path' => '/quem-somos/historia', 'key' => 'historia'],
                ['path' => '/quem-somos/missao', 'key' => 'missao'],
                ['path' => '/quem-somos/valores', 'key' => 'valores'],
                ['path' => '/quem-somos/fundador', 'key' => 'fundador'],
                ['path' => '/quem-somos/nossa-marca', 'key' => 'nossa-marca'],
                ['path' => '/quem-somos/gestao-cede', 'key' => 'gestao-cede'],
                ['path' => '/quem-somos/estatuto', 'key' => 'estatuto'],
                ['path' => '/quem-somos/base-de-conhecimento', 'key' => 'biblioteca'],
            ],
        ],
        [
            'key' => 'estudos',
            'base' => '/estudos',
            'items' => [
                ['path' => '/estudos', 'label' => 'Visão geral'],
                ['path' => '/estudos/eade', 'key' => 'eade'],
                ['path' => '/estudos/esde', 'key' => 'esde'],
                ['path' => '/estudos/palestras', 'key' => 'palestras'],
                ['path' => '/estudos/atendimento-fraterno', 'key' => 'atendimento-fraterno'],
            ],
        ],
        [
            'key' => 'agenda',
            'base' => '/agenda',
            'items' => [
                ['path' => '/agenda', 'label' => 'Visão geral'],
                ['path' => '/agenda/estudo-do-evangelho', 'key' => 'estudo-do-evangelho'],
                ['path' => '/agenda/palestra-publica', 'key' => 'palestra-publica'],
                ['path' => '/agenda/juventude-espirita', 'key' => 'juventude-espirita'],
            ],
        ],
        [
            'key' => 'loja',
            'base' => '/loja',
            'items' => [
                ['path' => '/loja', 'label' => 'Visão geral'],
                ['path' => '/loja/livraria', 'label' => 'NatalCode Labs'],
                ['path' => '/loja/bazar', 'key' => 'bazar'],
            ],
        ],
    ],
    'links_before_groups' => [
        [
            'path' => '/',
            'key' => 'inicio',
        ],
    ],
    'links_after_groups' => [
        [
            'path' => '/contato',
            'key' => 'contato',
        ],
    ],
];
