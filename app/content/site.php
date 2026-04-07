<?php

declare(strict_types=1);

return [
    'name' => 'NatalCode',
    'legalName' => 'NatalCode Agencia Digital',
    'tagline' => 'Estrategia, design e tecnologia para negocios digitais.',
    'brand' => [
        'homeHref' => '/',
        'logoSrc' => '/assets/img/brand/natalcode_logo_horizontal_black_1158x314.png',
        'logoLightSrc' => '/assets/img/brand/natalcode_logo_horizontal_black_1158x314.png',
        'logoDarkSrc' => '/assets/img/brand/natalcode_logo_horizontal_white_1158x314.png',
        'logoAlt' => 'NatalCode',
        'motto' => 'Codigo com proposito, design com resultado.',
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
        'statuteExcerpt' => 'A NatalCode e uma agencia digital focada em estrategia, design e desenvolvimento web, com atuacao orientada a resultado e experiencia do usuario.',
        'contactSummary' => 'Atendemos empresas, profissionais e projetos que precisam de presenca digital clara, rapida e confiavel.',
        'footerSummary' => 'Agencia digital • Desenvolvimento web • SEO • Performance',
    ],
    'footer' => [
        'kicker' => 'Presenca digital orientada a resultado',
        'description' => 'Da landing page ao sistema sob medida, estruturamos projetos digitais com visao de produto, performance e evolucao continua.',
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
        'contactLead' => 'Envie sua ideia e retornamos com proximo passo, escopo inicial e estimativa.',
        'bottomNote' => 'Atendimento nacional',
        'cnpj' => '04.242.556/0001-45',
    ],
];
