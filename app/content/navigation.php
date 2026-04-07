<?php

declare(strict_types=1);

return [
    'labels' => [
        'inicio' => 'Inicio',
        'quem-somos' => 'Quem Somos',
        'missao' => 'Missao',
        'valores' => 'Valores',
        'historia' => 'Historia',
        'fundador' => 'Lideranca',
        'estatuto' => 'Diretrizes',
        'nossa-marca' => 'Nossa Marca',
        'gestao-cede' => 'Gestao',
        'estudos' => 'Servicos',
        'esde' => 'Landing Pages',
        'eade' => 'Sites Institucionais',
        'palestras' => 'SEO e Conteudo',
        'atendimento-fraterno' => 'Suporte e Manutencao',
        'agenda' => 'Processo',
        'estudo-do-evangelho' => 'Descoberta',
        'palestra-publica' => 'Implementacao',
        'juventude-espirita' => 'Otimizacao',
        'loja' => 'Recursos',
        'bazar' => 'Materiais',
        'livraria' => 'NatalCode Labs',
        'livraria-ii' => 'NatalCode Labs',
        'livraria-auta-de-sousa' => 'NatalCode Labs',
        'faq' => 'FAQ',
        'biblioteca' => 'Base de conhecimento',
        'base-de-conhecimento' => 'Base de conhecimento',
        'doutrina' => 'Estrategia',
        'participacao' => 'Contratacao',
        'praticas' => 'Entrega',
        'politica-de-privacidade' => 'Politica de Privacidade',
        'dados-de-acesso' => 'Dados de acesso',
        'termos-de-uso' => 'Termos de Uso',
        'contato' => 'Contato',
    ],
    'menu' => [
        [
            'key' => 'quem-somos',
            'base' => '/quem-somos',
            'items' => [
                ['path' => '/quem-somos', 'label' => 'Visao geral'],
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
                ['path' => '/estudos', 'label' => 'Visao geral'],
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
                ['path' => '/agenda', 'label' => 'Visao geral'],
                ['path' => '/agenda/estudo-do-evangelho', 'key' => 'estudo-do-evangelho'],
                ['path' => '/agenda/palestra-publica', 'key' => 'palestra-publica'],
                ['path' => '/agenda/juventude-espirita', 'key' => 'juventude-espirita'],
            ],
        ],
        [
            'key' => 'loja',
            'base' => '/loja',
            'items' => [
                ['path' => '/loja', 'label' => 'Visao geral'],
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
