<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Home Content Map
|--------------------------------------------------------------------------
| Este arquivo centraliza o conteúdo textual e estrutural usado pelas
| páginas públicas (home e páginas derivadas).
|
| Convenções:
| - `sections.*` define cabeçalhos e textos principais de cada bloco.
| - Arrays de itens (`*Items`, `*Actions`, `*Cards`) alimentam grids, CTAs
|   e listas renderizadas pelos templates Twig.
| - Chaves como `delay`, `sizes`, `srcset` controlam animação e responsividade.
|--------------------------------------------------------------------------
*/
return [
    // Cabeçalhos e copy principal de cada seção da home.
    'sections' => [
        // Hero principal (mensagem, mídia, QR e parâmetros do carrossel).
        'hero' => [
            'kicker' => 'Sites e sistemas para negócios',
            'title' => 'Software, sites e produtos digitais que convertem',
            'tagline' => 'Para empresas que precisam lançar rápido e evoluir com segurança',
            'lead' => 'Unimos UX, engenharia e conteúdo para aumentar demanda e reduzir retrabalho. Projetos recentes incluem +38% na taxa de contato em 45 dias e lançamentos em até 21 dias.',
            'imageSrc' => '/assets/img/hero/home/natalcode1-1920.jpg',
            'images' => [
                [
                    'src' => '/assets/img/hero/home/natalcode1-1920.jpg',
                    'srcset' => '/assets/img/hero/home/natalcode1-768.jpg 768w, /assets/img/hero/home/natalcode1-1280.jpg 1280w, /assets/img/hero/home/natalcode1-1920.jpg 1920w',
                    'webpSrcset' => '/assets/img/hero/home/natalcode1-768.webp 768w, /assets/img/hero/home/natalcode1-1280.webp 1280w, /assets/img/hero/home/natalcode1-1920.webp 1920w',
                    'avifSrcset' => '/assets/img/hero/home/natalcode1-768.avif 768w, /assets/img/hero/home/natalcode1-1280.avif 1280w, /assets/img/hero/home/natalcode1-1920.avif 1920w',
                    'sizes' => '100vw',
                    'mobileSrcset' => '/assets/img/hero/home/natalcode1-mobile-480.jpg 480w, /assets/img/hero/home/natalcode1-mobile-768.jpg 768w, /assets/img/hero/home/natalcode1-mobile-1024.jpg 1024w',
                    'mobileWebpSrcset' => '/assets/img/hero/home/natalcode1-mobile-480.webp 480w, /assets/img/hero/home/natalcode1-mobile-768.webp 768w, /assets/img/hero/home/natalcode1-mobile-1024.webp 1024w',
                    'mobileAvifSrcset' => '/assets/img/hero/home/natalcode1-mobile-480.avif 480w, /assets/img/hero/home/natalcode1-mobile-768.avif 768w, /assets/img/hero/home/natalcode1-mobile-1024.avif 1024w',
                    'mobileSizes' => '100vw',
                    'kicker' => 'Sites e sistemas para negócios',
                    'badge' => 'Sites e sistemas para negócios',
                    'title' => 'Software, sites e produtos digitais que convertem',
                    'tagline' => 'Para empresas que precisam lançar rápido e evoluir com segurança',
                    'lead' => 'Unimos UX, engenharia e conteúdo para aumentar demanda e reduzir retrabalho. Projetos recentes incluem +38% na taxa de contato em 45 dias e lançamentos em até 21 dias.',
                    'imageAlt' => 'Interface da NatalCode com visual SaaS e proposta de valor em destaque',
                ],
                [
                    'src' => '/assets/img/hero/home/natalcode2-1920.jpg',
                    'srcset' => '/assets/img/hero/home/natalcode2-768.jpg 768w, /assets/img/hero/home/natalcode2-1280.jpg 1280w, /assets/img/hero/home/natalcode2-1920.jpg 1920w',
                    'webpSrcset' => '/assets/img/hero/home/natalcode2-768.webp 768w, /assets/img/hero/home/natalcode2-1280.webp 1280w, /assets/img/hero/home/natalcode2-1920.webp 1920w',
                    'avifSrcset' => '/assets/img/hero/home/natalcode2-768.avif 768w, /assets/img/hero/home/natalcode2-1280.avif 1280w, /assets/img/hero/home/natalcode2-1920.avif 1920w',
                    'sizes' => '100vw',
                    'mobileSrcset' => '/assets/img/hero/home/natalcode2-mobile-480.jpg 480w, /assets/img/hero/home/natalcode2-mobile-768.jpg 768w, /assets/img/hero/home/natalcode2-mobile-1024.jpg 1024w',
                    'mobileWebpSrcset' => '/assets/img/hero/home/natalcode2-mobile-480.webp 480w, /assets/img/hero/home/natalcode2-mobile-768.webp 768w, /assets/img/hero/home/natalcode2-mobile-1024.webp 1024w',
                    'mobileAvifSrcset' => '/assets/img/hero/home/natalcode2-mobile-480.avif 480w, /assets/img/hero/home/natalcode2-mobile-768.avif 768w, /assets/img/hero/home/natalcode2-mobile-1024.avif 1024w',
                    'mobileSizes' => '100vw',
                    'kicker' => 'Lançamento acelerado sem perder qualidade',
                    'badge' => 'Lançamento acelerado sem perder qualidade',
                    'title' => 'Seu site, do briefing à publicação',
                    'tagline' => 'Sprints curtas, validação rápida e entregas contínuas para ganhar tempo de mercado',
                    'lead' => 'Organizamos arquitetura, conteúdo e interface para você sair do rascunho para produção com previsibilidade, checklist técnico e acompanhamento de ponta a ponta.',
                    'imageAlt' => 'Tela de planejamento e execução rápida de projeto digital da NatalCode',
                ],
                [
                    'src' => '/assets/img/hero/home/natalcode3-1920.jpg',
                    'srcset' => '/assets/img/hero/home/natalcode3-768.jpg 768w, /assets/img/hero/home/natalcode3-1280.jpg 1280w, /assets/img/hero/home/natalcode3-1920.jpg 1920w',
                    'webpSrcset' => '/assets/img/hero/home/natalcode3-768.webp 768w, /assets/img/hero/home/natalcode3-1280.webp 1280w, /assets/img/hero/home/natalcode3-1920.webp 1920w',
                    'avifSrcset' => '/assets/img/hero/home/natalcode3-768.avif 768w, /assets/img/hero/home/natalcode3-1280.avif 1280w, /assets/img/hero/home/natalcode3-1920.avif 1920w',
                    'sizes' => '100vw',
                    'mobileSrcset' => '/assets/img/hero/home/natalcode3-mobile-480.jpg 480w, /assets/img/hero/home/natalcode3-mobile-768.jpg 768w, /assets/img/hero/home/natalcode3-mobile-1024.jpg 1024w',
                    'mobileWebpSrcset' => '/assets/img/hero/home/natalcode3-mobile-480.webp 480w, /assets/img/hero/home/natalcode3-mobile-768.webp 768w, /assets/img/hero/home/natalcode3-mobile-1024.webp 1024w',
                    'mobileAvifSrcset' => '/assets/img/hero/home/natalcode3-mobile-480.avif 480w, /assets/img/hero/home/natalcode3-mobile-768.avif 768w, /assets/img/hero/home/natalcode3-mobile-1024.avif 1024w',
                    'mobileSizes' => '100vw',
                    'kicker' => 'Conteúdo estratégico e UX orientada a resultado',
                    'badge' => 'Conteúdo estratégico e UX orientada a resultado',
                    'title' => 'Landing pages que transformam visitas em contatos',
                    'tagline' => 'Copy comercial, prova social e CTA claro para aumentar a taxa de resposta',
                    'lead' => 'A combinação de narrativa de oferta, performance e instrumentação de dados já gerou ganhos de +38% na taxa de contato em cenários recentes.',
                    'imageAlt' => 'Tela de landing page com elementos de conversão e métricas',
                ],
                [
                    'src' => '/assets/img/hero/home/natalcode4-1920.jpg',
                    'srcset' => '/assets/img/hero/home/natalcode4-768.jpg 768w, /assets/img/hero/home/natalcode4-1280.jpg 1280w, /assets/img/hero/home/natalcode4-1920.jpg 1920w',
                    'webpSrcset' => '/assets/img/hero/home/natalcode4-768.webp 768w, /assets/img/hero/home/natalcode4-1280.webp 1280w, /assets/img/hero/home/natalcode4-1920.webp 1920w',
                    'avifSrcset' => '/assets/img/hero/home/natalcode4-768.avif 768w, /assets/img/hero/home/natalcode4-1280.avif 1280w, /assets/img/hero/home/natalcode4-1920.avif 1920w',
                    'sizes' => '100vw',
                    'mobileSrcset' => '/assets/img/hero/home/natalcode4-mobile-480.jpg 480w, /assets/img/hero/home/natalcode4-mobile-768.jpg 768w, /assets/img/hero/home/natalcode4-mobile-1024.jpg 1024w',
                    'mobileWebpSrcset' => '/assets/img/hero/home/natalcode4-mobile-480.webp 480w, /assets/img/hero/home/natalcode4-mobile-768.webp 768w, /assets/img/hero/home/natalcode4-mobile-1024.webp 1024w',
                    'mobileAvifSrcset' => '/assets/img/hero/home/natalcode4-mobile-480.avif 480w, /assets/img/hero/home/natalcode4-mobile-768.avif 768w, /assets/img/hero/home/natalcode4-mobile-1024.avif 1024w',
                    'mobileSizes' => '100vw',
                    'kicker' => 'Base técnica pronta para escalar',
                    'badge' => 'Base técnica pronta para escalar',
                    'title' => 'PHP moderno, Twig e governança de conteúdo',
                    'tagline' => 'Estrutura modular para evoluir com segurança, sem retrabalho a cada nova campanha',
                    'lead' => 'Entregamos uma stack enxuta para seu time publicar, testar e iterar com autonomia, mantendo consistência visual, velocidade e manutenção simples.',
                    'imageAlt' => 'Interface de plataforma escalável com arquitetura modular em destaque',
                ],
            ],
            'imageIntervalMs' => 8000,
            'imageAlt' => 'Tela de apresentação de serviços digitais da NatalCode',
            'imageDelay' => 180,
            'actionsDelay' => 140,
            'showPanel' => false,
            'panelTitle' => 'Nossas Soluções',
            'panelDelay' => 180,
            'qrTitle' => 'Acesse no celular',
            'qrLead' => 'Abra natalcode.com.br no seu dispositivo para enviar briefing e iniciar seu projeto.',
            'qrUrl' => 'https://natalcode.com.br',
            'qrImage' => '/assets/img/brand/natalcode1.png',
            'qrAlt' => 'Marca NatalCode',
        ],
        // Cabeçalho da seção de serviços/estudos.
        'features' => [
            'kicker' => 'Serviços',
            'title' => 'Presença digital com padrão profissional',
            'lead' => 'Combinamos estratégia de conteúdo, UX e engenharia para acelerar a entrega e reduzir retrabalho.',
        ],
        // Cabeçalho da seção "Quem Somos" na home (social proof).
        'socialProof' => [
            //'kicker' => 'Quem Somos',
            'title' => 'NatalCode - Soluções digitais',
            //'lead' => 'A NatalCode desenvolve landing pages, sites, sistemas e produtos digitais para negócios. Atuamos do planejamento à publicação, com acompanhamento contínuo de performance.',
            //'leadSecondary' => 'Nosso objetivo é claro: transformar metas de negócio em páginas e sistemas que vendem melhor, comunicam com clareza e evoluem junto com a operação.',
            'trustGridLabel' => 'Soluções Orientadas a Resultado',
            'trustGridDescription' => 'A NatalCode projeta e desenvolve landing pages, sites, sistemas e produtos digitais orientados a resultados. Atuamos de ponta a ponta — do planejamento estratégico à publicação — com monitoramento contínuo de performance. Nosso foco é transformar objetivos de negócio em soluções digitais que convertem mais, comunicam com clareza e evoluem de forma consistente junto à operação.',
        ],
        // Cabeçalho da seção de depoimentos.
        'testimonials' => [
            'kicker' => 'Clientes',
            'title' => 'Resultados de quem escolheu a NatalCode',
            'lead' => 'Depoimentos com indicadores reais de conversão, prazo e retorno em projetos de site e landing page.',
        ],

        // Cabeçalho da seção de processo/roadmap.
        // Nao renderiza na Home atual; usado pelas páginas da agenda.
        'roadmap' => [
            'kicker' => 'Processo',
            'title' => 'Como trabalhamos em cada projeto',
            'lead' => 'Fluxo simples, objetivo e transparente: descobrimos contexto, executamos com qualidade e otimizamos com dados reais.',
        ],
        // Cabeçalho da seção FAQ.
        // Nao renderiza na Home atual; usado na página /faq.
        'faq' => [
            'kicker' => 'Dúvidas',
            'title' => 'Perguntas Frequentes',
            'lead' => 'Respostas diretas sobre prazo, investimento, manutenção e contratação dos serviços da NatalCode.',
        ],
        /*
        | Bloco orfao (desativado):
        | - sections.donation era consumido por templates/home/donation.twig.
        | - O template donation.twig nao esta incluido nas paginas atuais.
        |
        | 'donation' => [
        |     'kicker' => 'Orçamento rapido',
        |     'title' => 'Inicie seu projeto',
        |     'lead' => 'Envie seu briefing e receba direcionamento técnico com escopo inicial para o seu momento de negócio.',
        |     'leadSecondary' => 'Escolha o canal mais conveniente para falar com nosso time.',
        | ],
        */



        // Cabeçalho da chamada final da home.
        'cta' => [
            'kicker' => 'Próximo passo',
            'title' => 'Vamos construir sua presença digital',
            'lead' => 'Se você precisa lançar, melhorar conversão ou organizar sua operação online, a NatalCode estrutura o projeto com método e execução previsivel.',
            'actionsDelay' => 160,
        ],
    ],
    // Botões de ação exibidos no hero.
    'heroActions' => [
        [
            'label' => 'Solicitar orçamento',
            'href' => '/contato',
            'class' => 'nc-btn nc-btn-primary',
            'loadingOnClick' => false,
        ],
        [
            'label' => 'Ver serviços',
            'href' => '/estudos',
            'class' => 'nc-btn nc-btn-primary',
            'loadingOnClick' => false,
        ],
    ],
    // Indicadores exibidos no painel de métricas do hero.
    'heroMetrics' => [
        [
            'value' => '120+',
            'label' => 'Projetos entregues',
            'delay' => 220,
        ],
        [
            'value' => '8+ anos',
            'label' => 'Experiência digital',
            'delay' => 280,
        ],
        [
            'value' => '24h',
            'label' => 'Retorno comercial',
            'delay' => 340,
        ],
        [
            'value' => '99.9%',
            'label' => 'Foco em estabilidade',
            'delay' => 400,
        ],
    ],
    // Cards da seção de serviços em destaque.
    'featuresItems' => [
        [
            'title' => 'Landing Pages de Conversão',
            'description' => 'Páginas objetivas para captação de leads, vendas e campanhas com copy orientada a resultado.',
            'href' => '/estudos/esde',
            'linkLabel' => 'Ver detalhes',
            'delay' => 120,
        ],
        [
            'title' => 'Sites Institucionais',
            'description' => 'Sites completos para reforcar posicionamento, apresentar serviços e facilitar contato comercial.',
            'href' => '/estudos/eade',
            'linkLabel' => 'Conhecer solucao',
            'delay' => 180,
        ],
        [
            'title' => 'SEO e Evolução Continua',
            'description' => 'Ajustes técnicos e de conteúdo para ganhar visibilidade orgânica e melhorar performance.',
            'href' => '/estudos/palestras',
            'linkLabel' => 'Ver como funciona',
            'delay' => 240,
        ],
    ],
    // Conteúdo detalhado das páginas de serviços.
    'studiesPages' => [
        'eade' => [
            'kicker' => 'Serviços',
            'title' => 'Sites Institucionais',
            'lead' => 'Estruturamos sites institucionais com arquitetura clara, visual consistente e base técnica pronta para crescimento.',
            'topics' => [
                [
                    'step' => '01',
                    'title' => 'Arquitetura de informação',
                    'description' => 'Organizamos páginas, navegação e mensagens para facilitar entendimento e tomada de decisão do visitante.',
                    'delay' => 120,
                ],
                [
                    'step' => '02',
                    'title' => 'Identidade e credibilidade',
                    'description' => 'Aplicamos direção visual e padrões de interface que fortalecem percepcao de marca.',
                    'delay' => 180,
                ],
                [
                    'step' => '03',
                    'title' => 'Base para evolução',
                    'description' => 'Entrega com estrutura de conteúdo e componentes reaproveitaveis para novas páginas.',
                    'delay' => 240,
                ],
            ],
        ],
        'esde' => [
            'kicker' => 'Serviços',
            'title' => 'Landing Pages',
            'lead' => 'Desenvolvemos landing pages focadas em conversão com narrativa comercial, prova social e CTA bem definido.',
            'topics' => [
                [
                    'step' => '01',
                    'title' => 'Mensagem orientada a oferta',
                    'description' => 'Copy clara para destacar proposta de valor, diferenciais e urgencia de forma objetiva.',
                    'delay' => 120,
                ],
                [
                    'step' => '02',
                    'title' => 'Performance e responsividade',
                    'description' => 'Páginas leves, rápidas e adaptadas para mobile, sem perder qualidade visual.',
                    'delay' => 180,
                ],
                [
                    'step' => '03',
                    'title' => 'Mediacao de resultados',
                    'description' => 'Configuração para acompanhar leads, cliques e comportamento com dados reais.',
                    'delay' => 240,
                ],
            ],
        ],
        'palestras' => [
            'kicker' => 'Serviços',
            'title' => 'SEO e Conteúdo',
            'lead' => 'Trabalho continuo de SEO técnico e conteúdo para aumentar autoridade, alcance organico e qualidade do tráfego.',
            'topics' => [
                [
                    'step' => '01',
                    'title' => 'Diagnóstico técnico',
                    'description' => 'Mapeamos pontos de performance, indexação, estrutura semântica e experiência da página.',
                    'delay' => 120,
                ],
                [
                    'step' => '02',
                    'title' => 'Plano editorial',
                    'description' => 'Definimos pautas e clusters de conteúdo com base em intencao de busca e jornada do cliente.',
                    'delay' => 180,
                ],
                [
                    'step' => '03',
                    'title' => 'Ciclos de otimização',
                    'description' => 'Ajustes recorrentes guiados por ranking, cliques e taxa de conversão.',
                    'delay' => 240,
                ],
            ],
        ],
        'atendimento-fraterno' => [
            'kicker' => 'Serviços',
            'title' => 'Suporte e Manutenção',
            'lead' => 'Mantemos seu projeto atualizado, seguro e funcional com acompanhamento técnico e melhorias iterativas.',
            'topics' => [
                [
                    'step' => '01',
                    'title' => 'Correções e estabilidade',
                    'description' => 'Monitoramos incidentes, corrigimos falhas e reduzimos impacto operacional.',
                    'delay' => 120,
                ],
                [
                    'step' => '02',
                    'title' => 'Evolução por prioridade',
                    'description' => 'Implementamos ajustes de acordo com impacto no negócio e na experiência do usuário.',
                    'delay' => 180,
                ],
                [
                    'step' => '03',
                    'title' => 'Segurança operacional',
                    'description' => 'Boas práticas de backup, atualização e proteção para manter o ambiente confiável.',
                    'delay' => 240,
                ],
            ],
        ],
    ],
    // Conteúdo detalhado das páginas institucionais "Quem Somos".
    'aboutPages' => [
        'missao' => [
            'kicker' => 'Quem Somos',
            'title' => 'Nossa Missao',
            'lead' => 'Ajudar empresas a vender mais e operar melhor por meio de produtos digitais bem construidos.',
            'topics' => [
                [
                    'step' => '01',
                    'title' => 'Estratégia com foco em negócio',
                    'description' => 'Traduzimos objetivos comerciais em prioridades de produto e comunicação.',
                    'delay' => 120,
                ],
                [
                    'step' => '02',
                    'title' => 'Execução com padrão técnico',
                    'description' => 'Entregamos com organização de código, documentacao e previsibilidade.',
                    'delay' => 180,
                ],
                [

                    'step' => '03',
                    'title' => 'Relacao de longo prazo',
                    'description' => 'Acompanhamos a evolução do projeto com melhorias e suporte contínuo.',
                    'delay' => 240,
                ],
            ],
        ],
        'valores' => [
            'kicker' => 'Quem Somos',
            'title' => 'Nossos Valores',
            'lead' => 'Trabalhamos com transparência, responsabilidade técnica e compromisso com resultado.',
            'topics' => [
                [
                    'step' => '01',
                    'title' => 'Clareza',
                    'description' => 'Escopo, prazos e riscos comunicados de forma objetiva em cada etapa.',
                    'delay' => 120,
                ],
                [
                    'step' => '02',
                    'title' => 'Qualidade',
                    'description' => 'Padrão de implementação com foco em manutenção, segurança e performance.',
                    'delay' => 180,
                ],
                [
                    'step' => '03',
                    'title' => 'Parceria',
                    'description' => 'Decisões alinhadas com o contexto real do negócio do cliente.',
                    'delay' => 240,
                ],
            ],
        ],
        'historia' => [
            'kicker' => 'Quem Somos',
            'title' => 'Nossa Historia',
            'lead' => 'A NatalCode nasceu para unir design e desenvolvimento em uma entrega digital mais eficiente.',
            'topics' => [
                [
                    'step' => '01',
                    'title' => 'Início orientado a projetos web',
                    'description' => 'Começamos atendendo demandas de páginas institucionais e campanhas de captação.',
                    'delay' => 120,
                ],
                [
                    'step' => '02',
                    'title' => 'Evolução para produto digital',
                    'description' => 'Expandimos para fluxos de conversão, automação e painéis de gestão.',
                    'delay' => 180,
                ],
                [
                    'step' => '03',
                    'title' => 'Operação contínua',
                    'description' => 'Hoje atuamos como parceiro técnico para evolução constante de presença digital.',
                    'delay' => 240,
                ],
            ],
        ],
        'fundador' => [
            'kicker' => 'Quem Somos',
            'title' => 'Governança',
            'lead' => 'Do conceito à implementação, a NatalCode orquestra todo o ciclo de vida do seu projeto, com expertise técnica e visão estratégica. Resultado? Sistemas que não apenas funcionam, mas performam e evoluem.',
            'name' => '',
            'role' => 'Founder & CEO | Lead Developer at NatalCode',
            'photo' => '/assets/img/techlead.png',
            'photo_alt' => 'Retrato da governança da NatalCode',
            'intro' => [
                'A NatalCode é liderada por seu fundador, que atua diretamente na direção estratégica e na execução técnica dos projetos. Esse modelo garante alinhamento entre visão de negócio e excelência tecnológica, proporcionando soluções eficientes, bem estruturadas e orientadas a resultados. ',
                'Nossa atuação conecta estratégia, design e engenharia em um processo disciplinado: começamos com diagnóstico e definição de metas, avançamos para implementação técnica com padrão de qualidade e sustentamos a evolução com leitura de dados, testes e ciclos contínuos de melhoria.',
                'A NatalCode foi estruturada para resolver desafios concretos de posicionamento digital, geração de demanda e eficiência comercial em negócios que precisam crescer com previsibilidade.',
            ],
            'quote' => 'Um bom projeto digital nasce de contexto claro, execução disciplinada e melhoria constante.',
            'quote_attribution' => 'Visão de governança NatalCode',
            'topics' => [
                [
                    'step' => '01',
                    'title' => 'Visão de produto',
                    'description' => 'Cada entrega precisa resolver problema real e gerar impacto mensuravel.',
                    'delay' => 120,
                ],
                [
                    'step' => '02',
                    'title' => 'Operação com processo',
                    'description' => 'Padronizamos etapas para reduzir retrabalho e acelerar entregas.',
                    'delay' => 180,
                ],
                [
                    'step' => '03',
                    'title' => 'Compromisso com evolução',
                    'description' => 'Projetos são tratados como ativos vivos, sempre em ciclo de melhoria.',
                    'delay' => 240,
                ],
            ],
        ],
        'estatuto' => [
            'kicker' => 'Quem Somos',
            'title' => 'Diretrizes Institucionais',
            'lead' => 'A NatalCode atua como agência digital privada, com foco em desenvolvimento web, design de interface e consultoria de presença digital.',
            'topics' => [
                [
                    'step' => '01',
                    'title' => 'Atuação',
                    'description' => 'Prestação de serviços digitais para empresas, profissionais e projetos em fase de expansão.',
                    'delay' => 120,
                ],
                [
                    'step' => '02',
                    'title' => 'Modelo de trabalho',
                    'description' => 'Escopo acordado por projeto, acompanhamento por etapas e revisões planejadas.',
                    'delay' => 180,
                ],
                [
                    'step' => '03',
                    'title' => 'Compromisso',
                    'description' => 'Entrega com transparência, responsabilidade técnica e alinhamento com objetivos comerciais.',
                    'delay' => 240,
                ],
            ],
        ],
    ],
    // Conteúdo da área/labs (bookshop/laboratório).
    'bookshopPages' => [
        'auta-de-sousa' => [
            'kicker' => 'Labs',
            'title' => 'NatalCode Labs',
            'lead' => 'Espaço dedicado a experimentos, templates e aceleradores que usamos para acelerar entregas.',
            'name' => 'NatalCode Labs',
            'role' => 'Pesquisa e desenvolvimento',
            'photo' => '/assets/img/face1_620_620.png',
            'photo_alt' => 'Identidade visual da NatalCode Labs',
            'intro' => [
                'No Labs testamos interfaces, componentes e fluxos antes de levar para produção.',
                'Esse processo reduz risco, melhora qualidade e antecipa decisões de produto.',
            ],
            'quote' => 'Experimentar cedo evita retrabalho tarde.',
            'quote_attribution' => 'NatalCode Labs',
            'topics' => [
                [
                    'step' => '01',
                    'title' => 'Prototipos funcionais',
                    'description' => 'Validamos layout, copy e navegação com cenarios reais de uso.',
                    'delay' => 120,
                ],
                [
                    'step' => '02',
                    'title' => 'Componentes reutilizaveis',
                    'description' => 'Criamos blocos padrão para acelerar novos projetos sem perder consistência.',
                    'delay' => 180,
                ],
                [
                    'step' => '03',
                    'title' => 'Aprendizado continuo',
                    'description' => 'Documentamos decisões técnicas e padrões para evolução do time e dos produtos.',
                    'delay' => 240,
                ],
            ],
        ],
    ],
    // Cards visuais de confiança da seção social proof.
    'socialProofTrustCards' => [
        [
            'image' => '/assets/img/trust/trust-landing-640.jpg',
            'srcset' => '/assets/img/trust/trust-landing-420.jpg 420w, /assets/img/trust/trust-landing-640.jpg 640w, /assets/img/trust/trust-landing-960.jpg 960w',
            'webpSrcset' => '/assets/img/trust/trust-landing-420.webp 420w, /assets/img/trust/trust-landing-640.webp 640w, /assets/img/trust/trust-landing-960.webp 960w',
            'avifSrcset' => '/assets/img/trust/trust-landing-420.avif 420w, /assets/img/trust/trust-landing-640.avif 640w, /assets/img/trust/trust-landing-960.avif 960w',
            'sizes' => '(max-width: 700px) 92vw, (max-width: 1100px) 46vw, 360px',
            'alt' => 'Projeto web com identidade de produto',
            'title' => 'Landing Pages',
            'text' => 'Estruturas orientadas à conversão para campanhas e geração qualificada de leads.',
        ],
        [
            'image' => '/assets/img/trust/trust-site-640.jpg',
            'srcset' => '/assets/img/trust/trust-site-420.jpg 420w, /assets/img/trust/trust-site-640.jpg 640w, /assets/img/trust/trust-site-960.jpg 960w',
            'webpSrcset' => '/assets/img/trust/trust-site-420.webp 420w, /assets/img/trust/trust-site-640.webp 640w, /assets/img/trust/trust-site-960.webp 960w',
            'avifSrcset' => '/assets/img/trust/trust-site-420.avif 420w, /assets/img/trust/trust-site-640.avif 640w, /assets/img/trust/trust-site-960.avif 960w',
            'sizes' => '(max-width: 700px) 92vw, (max-width: 1100px) 46vw, 360px',
            'alt' => 'Projeto institucional com arquitetura clara',
            'title' => 'Sites Institucionais',
            'text' => 'Conteúdo pensado para comunicar valor, gerar autoridade e posicionar sua marca com clareza.',
        ],
        [
            'image' => '/assets/img/trust/trust-seo-640.jpg',
            'srcset' => '/assets/img/trust/trust-seo-420.jpg 420w, /assets/img/trust/trust-seo-640.jpg 640w, /assets/img/trust/trust-seo-960.jpg 960w',
            'webpSrcset' => '/assets/img/trust/trust-seo-420.webp 420w, /assets/img/trust/trust-seo-640.webp 640w, /assets/img/trust/trust-seo-960.webp 960w',
            'avifSrcset' => '/assets/img/trust/trust-seo-420.avif 420w, /assets/img/trust/trust-seo-640.avif 640w, /assets/img/trust/trust-seo-960.avif 960w',
            'sizes' => '(max-width: 700px) 92vw, (max-width: 1100px) 46vw, 360px',
            'alt' => 'Análise de desempenho e crescimento',
            'title' => 'SEO e Performance',
            'text' => 'Ajustes constantes com base em dados para melhorar desempenho e conversão.',
        ],
    ],
    // Depoimentos usados na seção de testemunhos.
    'socialProofTestimonials' => [
        [
            'avatar' => 'https://randomuser.me/api/portraits/women/44.jpg',
            'alt' => 'Marina Lima',
            'name' => 'Marina Lima',
            'role' => 'Gestora comercial',
            'quote' => 'A taxa de contato subiu de 2,1% para 4,8% em 45 dias, mantendo o mesmo investimento em mídia. A página ficou clara e objetiva para o nosso público.',
        ],
        [
            'avatar' => 'https://randomuser.me/api/portraits/men/32.jpg',
            'alt' => 'Joao Costa',
            'name' => 'Joao Costa',
            'role' => 'Empreendedor',
            'quote' => 'Fechamos o novo site em 24 dias, antes do prazo de 30, e o tempo médio de permanencia passou de 1m12 para 2m49 no primeiro mês.',
        ],
        [
            'avatar' => 'https://randomuser.me/api/portraits/women/68.jpg',
            'alt' => 'Ana Bezerra',
            'name' => 'Ana Bezerra',
            'role' => 'Coordenadora de marketing',
            'quote' => 'Em 90 dias saímos de 18 para 47 palavras-chave no top 10 e aumentamos em 62% o tráfego orgânico qualificado, com base técnica pronta para escalar.',
        ],
        [
            'avatar' => 'https://randomuser.me/api/portraits/women/90.jpg',
            'alt' => 'Solange Rocha',
            'name' => 'Solange Rocha',
            'role' => 'Consultora',
            'quote' => 'Com ajustes quinzenais no pós-entrega, o CAC caiu 23% e o ROI das campanhas saiu de 1,9x para 3,4x em três meses.',
        ],
    ],
    /*
    | Bloco orfao (desativado):
    | - donationOptions era consumido por templates/home/donation.twig.
    | - O template donation.twig nao esta incluido nas paginas atuais.
    |
    | 'donationOptions' => [
    |     [
    |         'title' => 'Canal de Orçamento',
    |         'description' => 'Fale com nosso time para receber escopo inicial e proposta alinhada ao seu objetivo de negócio.',
    |         'qrImage' => '/assets/img/brand/natalcode1.png',
    |         'qrAlt' => 'Identidade visual NatalCode',
    |         'hint' => 'Use o botão para abrir a página de contato.',
    |         'href' => '/contato',
    |         'linkLabel' => 'Enviar briefing',
    |     ],
    | ],
    */
    // Etapas macro do processo exibido na home.
    'roadmapItems' => [
        [
            'phase' => 'Etapa 1',
            'title' => 'Descoberta e Diagnóstico',
            'description' => 'Levantamos objetivo, público e contexto técnico para definir escopo e prioridades.',
            'href' => '/agenda/estudo-do-evangelho',
            'linkLabel' => 'Ver detalhes da etapa',
            'status' => 'done',
        ],
        [
            'phase' => 'Etapa 2',
            'title' => 'Design e Implementação',
            'description' => 'Executamos interface, conteúdo e desenvolvimento com validações incrementais.',
            'href' => '/agenda/palestra-publica',
            'linkLabel' => 'Ver detalhes da etapa',
            'status' => 'current',
        ],
        [
            'phase' => 'Etapa 3',
            'title' => 'Publicação e Otimização',
            'description' => 'Entramos em produção com acompanhamento de desempenho e ajustes de melhoria.',
            'href' => '/agenda/juventude-espirita',
            'linkLabel' => 'Ver detalhes da etapa',
            'status' => 'planned',
        ],
    ],
    // Páginas detalhadas de cada etapa do processo.
    'agendaPages' => [
        'estudo-do-evangelho' => [
            'kicker' => 'Processo',
            'title' => 'Descoberta e Diagnóstico',
            'lead' => 'Mapeamento inicial para definir escopo, prioridades e indicadores de resultado.',
            'topics' => [
                [
                    'step' => '01',
                    'title' => 'Levantamento de contexto',
                    'description' => 'Entendemos produto, público, concorrencia e objetivo de negócio.',
                    'delay' => 120,
                ],
                [
                    'step' => '02',
                    'title' => 'Definição de escopo',
                    'description' => 'Formalizamos entregas, limites e prioridades do ciclo inicial.',
                    'delay' => 180,
                ],
                [
                    'step' => '03',
                    'title' => 'Plano de execução',
                    'description' => 'Organizamos cronograma, checkpoints e critérios de validação.',
                    'delay' => 240,
                ],
            ],
        ],
        'palestra-publica' => [
            'kicker' => 'Processo',
            'title' => 'Design e Implementação',
            'lead' => 'Transformamos estratégia em interface, código e experiência funcional.',
            'topics' => [
                [
                    'step' => '01',
                    'title' => 'Direção visual e copy',
                    'description' => 'Construimos narrativa de página e componentes para conversão.',
                    'delay' => 120,
                ],
                [
                    'step' => '02',
                    'title' => 'Desenvolvimento incremental',
                    'description' => 'Entrega por blocos revisaveis para reduzir risco e manter velocidade.',
                    'delay' => 180,
                ],
                [
                    'step' => '03',
                    'title' => 'Validação final',
                    'description' => 'Checklist técnico, ajustes e preparo para publicação.',
                    'delay' => 240,
                ],
            ],
        ],
        'juventude-espirita' => [
            'kicker' => 'Processo',
            'title' => 'Publicação e Otimização',
            'lead' => 'Pos-lancamento com monitoramento continuo para melhorar conversão e estabilidade.',
            'topics' => [
                [
                    'step' => '01',
                    'title' => 'Entrada em produção',
                    'description' => 'Publicação com revisão de performance, SEO básico e segurança.',
                    'delay' => 120,
                ],
                [
                    'step' => '02',
                    'title' => 'Análise de dados',
                    'description' => 'Leitura de comportamento para identificar gargalos e oportunidades.',
                    'delay' => 180,
                ],
                [
                    'step' => '03',
                    'title' => 'Melhoria continua',
                    'description' => 'Iterações de copy, UX e técnica para evoluir resultado ao longo do tempo.',
                    'delay' => 240,
                ],
            ],
        ],
    ],
    // Categorias do FAQ.
    'faqCategories' => [
        [
            'slug' => 'doutrina',
            'title' => 'Estratégia',
            'lead' => 'Escopo, planejamento e definição de prioridade.',
        ],
        [
            'slug' => 'participacao',
            'title' => 'Contratação',
            'lead' => 'Como iniciar, prazos e modelo de trabalho.',
        ],
        [
            'slug' => 'praticas',
            'title' => 'Entrega',
            'lead' => 'Publicação, manutenção e acompanhamento pos-lancamento.',
        ],
    ],
    // Perguntas e respostas do FAQ.
    'faqItems' => [
        [
            'question' => 'Quais tipos de projeto a NatalCode atende?',
            'answer' => 'Atendemos landing pages, sites institucionais, páginas de campanha e evolução de plataformas web existentes.',
            'category' => 'doutrina',
        ],
        [
            'question' => 'Como funciona o início do projeto?',
            'answer' => 'Começamos com um briefing de contexto, objetivo e público para montar escopo inicial com prioridades.',
            'category' => 'participacao',
        ],
        [
            'question' => 'Qual o prazo médio de entrega?',
            'answer' => 'Depende do escopo. Projetos enxutos podem ser entregues em semanas; estruturas maiores exigem fases adicionais.',
            'category' => 'participacao',
        ],
        [
            'question' => 'Vocês oferecem manutenção após publicar?',
            'answer' => 'Sim. Temos acompanhamento técnico para correção, melhorias e evolução continua do projeto.',
            'category' => 'praticas',
        ],
        [
            'question' => 'Posso contratar apenas uma landing page?',
            'answer' => 'Sim. Projetos de página única fazem parte do nosso escopo e podem evoluir depois para site completo.',
            'category' => 'participacao',
        ],
        [
            'question' => 'A NatalCode cuida de SEO?',
            'answer' => 'Sim. Trabalhamos SEO técnico, estrutura de conteúdo e melhorias com base em dados de busca e conversão.',
            'category' => 'doutrina',
        ],
        [
            'question' => 'Quais tecnologias voces usam?',
            'answer' => 'Utilizamos stack moderna em PHP, Twig, CSS e JavaScript, com foco em performance e manutenção simples.',
            'category' => 'doutrina',
        ],
        [
            'question' => 'Como e feito o acompanhamento das entregas?',
            'answer' => 'Trabalhamos por etapas com checkpoints claros, para você validar progresso e priorizar ajustes.',
            'category' => 'praticas',
        ],
        [
            'question' => 'A NatalCode atende fora de Natal?',
            'answer' => 'Sim. O atendimento pode ser remoto para todo o Brasil.',
            'category' => 'participacao',
        ],
        [
            'question' => 'Como solicitar um orçamento?',
            'answer' => 'Basta acessar a página de contato, enviar seu briefing e objetivo comercial. Retornamos com os próximos passos.',
            'category' => 'participacao',
        ],
    ],
    // Botões da chamada final da home.
    'ctaActions' => [
        [
            'label' => 'Solicitar proposta',
            'href' => '/contato',
            'class' => 'nc-btn nc-btn-primary',
            'loadingOnClick' => false,
        ],
    ],
];
