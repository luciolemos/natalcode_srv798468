<?php

declare(strict_types=1);

return [
    'name' => 'NatalCode',
    'legalName' => 'NatalCode Agência Digital',
    'tagline' => 'Estratégia, design e tecnologia para negócios digitais.',
    'brand' => [
        'homeHref' => '/',
        'logoSrc' => '/assets/img/brand/natalcode_logo_horizontal_black_1158x314.png',
        'logoLightSrc' => '/assets/img/brand/natalcode_logo_horizontal_black_1158x314.png',
        'logoDarkSrc' => '/assets/img/brand/natalcode_logo_horizontal_white_1158x314.png',
        'logoAlt' => 'NatalCode',
        'motto' => 'Código com proposito, design com resultado.',
    ],
    'contact' => [
        'email' => 'contato@natalcode.com.br',
        'address' => 'Atendimento remoto para todo o Brasil.',
        'mapUrl' => 'https://natalcode.com.br/contato',
    ],
    'social' => [
        'instagram' => [
            'url' => 'https://www.instagram.com/natalcode/',
            'label' => 'Instagram oficial: @natalcode',
        ],
    ],
    'institutional' => [
        'statuteExcerpt' => 'A NatalCode e uma agência digital focada em estratégia, design e desenvolvimento web, com atuação orientada a resultado e experiência do usuário.',
        'contactSummary' => 'Atendemos empresas, profissionais e projetos que precisam de presença digital clara, rápida e confiável.',
        'footerSummary' => 'Agência digital • Desenvolvimento web • SEO • Performance',
    ],
    'footer' => [
        'kicker' => 'Presença digital orientada a resultado',
        'description' => 'Da landing page ao sistema sob medida, estruturamos projetos digitais com visão de produto, performance e evolução continua.',
        'navGroups' => [
            [
                'title' => 'Institucional',
                'links' => [
                    ['path' => '/', 'key' => 'inicio'],
                    ['path' => '/quem-somos', 'key' => 'quem-somos'],
                    ['path' => '/quem-somos/historia', 'key' => 'historia'],
                    ['path' => '/quem-somos/fundador', 'key' => 'fundador'],
                    ['path' => '/quem-somos/nossa-marca', 'key' => 'nossa-marca'],
                    ['path' => '/quem-somos/estatuto', 'key' => 'estatuto'],
                    ['path' => '/quem-somos/gestao-cede', 'key' => 'gestao-cede'],
                    ['path' => '/quem-somos/base-de-conhecimento', 'key' => 'biblioteca'],
                    ['path' => '/contato', 'key' => 'contato'],
                    ['path' => '/politica-de-privacidade', 'key' => 'politica-de-privacidade'],
                    ['path' => '/termos-de-uso', 'key' => 'termos-de-uso'],
                ],
            ],
            [
                'title' => 'Recursos',
                'links' => [
                    ['path' => '/loja', 'key' => 'loja'],
                    ['path' => '/loja/livraria', 'key' => 'livraria'],
                    ['path' => '/loja/bazar', 'key' => 'bazar'],
                ],
            ],
        ],
        'contactKicker' => 'Vamos conversar',
        'contactTitle' => 'Fale com a NatalCode',
        'contactLead' => 'Envie sua ideia e retornamos com próximo passo, escopo inicial e estimativa.',
        'bottomNote' => 'Atendimento nacional',
        'cnpj' => '04.242.556/0001-45',
    ],
];
