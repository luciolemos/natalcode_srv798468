<?php

/*
|--------------------------------------------------------------------------
| Conteúdo do site da NatalCode
|--------------------------------------------------------------------------
|
| Textos, serviços, portfólio, processo e perguntas frequentes. As páginas
| leem tudo daqui: para mudar um texto, edite este arquivo.
|
| Regra: só publique números, depoimentos e casos que possam ser comprovados.
|
*/

return [

    'name' => 'NatalCode',
    'legal_name' => 'NatalCode Soluções Digitais',
    'tagline' => 'Sites e sistemas web para negócios que querem crescer.',
    'description' => 'A NatalCode cria landing pages, sites institucionais e sistemas web sob medida, com foco em conversão, performance e evolução contínua.',
    'url' => env('APP_URL', 'https://natalcode.com.br'),
    'share_image' => '/images/brand/share.png',
    'locale' => 'pt_BR',

    // Google Analytics 4 (G-XXXXXXX). Só carrega depois do consentimento do visitante.
    'analytics' => [
        'ga4' => env('SITE_GA4_ID'),
    ],

    'contact' => [
        'email' => 'contato@natalcode.com.br',
        'whatsapp' => env('SITE_WHATSAPP_NUMBER', '5584996360721'),
        'whatsapp_message' => 'Olá! Vim pelo site da NatalCode e quero conversar sobre um projeto.',
        'city' => 'Natal/RN',
        'coverage' => 'Atendimento remoto para todo o Brasil',
        // CNPJ: preencher com o número da NatalCode (o do site antigo era de outra instituição).
        'cnpj' => env('SITE_CNPJ', ''),
    ],

    // Data da versão vigente da Política de Privacidade (AAAA-MM-DD). Atualize ao mudar o texto.
    'legal' => [
        'privacy_updated_at' => '2026-11-26',
    ],

    'social' => [
        'instagram' => ['label' => 'Instagram', 'url' => 'https://www.instagram.com/natalcodern/'],
        'facebook' => ['label' => 'Facebook', 'url' => 'https://www.facebook.com/natalcodern/'],
        'github' => ['label' => 'GitHub', 'url' => 'https://github.com/luciolemos'],
        'linkedin' => ['label' => 'LinkedIn', 'url' => 'https://www.linkedin.com/in/lucciolemos/'],
    ],

    'nav' => [
        ['label' => 'Serviços', 'route' => 'services.index'],
        ['label' => 'Portfólio', 'route' => 'portfolio'],
        ['label' => 'Sobre', 'route' => 'about'],
        ['label' => 'Dúvidas', 'route' => 'faq'],
    ],

    'hero' => [
        'kicker' => 'sites e sistemas para negócios',
        'title' => 'Software, sites e produtos digitais que convertem',
        'lead' => 'Unimos estratégia, design e engenharia para tirar seu projeto do papel com prazo claro, código bem feito e acompanhamento depois da publicação.',
        'primary' => ['label' => 'Solicitar orçamento', 'route' => 'contact'],
        'secondary' => ['label' => 'Ver serviços', 'route' => 'services.index'],
        'highlights' => [
            'Projeto sob medida, sem template genérico',
            'Rápido no celular e pronto para o Google',
            'Suporte depois da entrega',
        ],
    ],

    /*
    | Serviços. A chave é o endereço da página: /servicos/{chave}.
    | "icon" usa os nomes do componente <x-icon>.
    */
    'services' => [
        'landing-pages' => [
            'icon' => 'target',
            'title' => 'Landing pages',
            'summary' => 'Páginas de campanha focadas em uma única ação: gerar contato, agendamento ou venda.',
            'lead' => 'Desenvolvemos landing pages focadas em conversão, com narrativa comercial, prova social e chamada para ação bem definida.',
            'topics' => [
                ['title' => 'Mensagem orientada à oferta', 'description' => 'Texto claro para destacar proposta de valor, diferenciais e urgência de forma objetiva.'],
                ['title' => 'Performance e responsividade', 'description' => 'Páginas leves, rápidas e adaptadas ao celular, sem perder qualidade visual.'],
                ['title' => 'Medição de resultados', 'description' => 'Configuração para acompanhar contatos, cliques e comportamento com dados reais.'],
            ],
            'ideal_for' => ['Campanhas no Google Ads e Meta Ads', 'Lançamento de produto ou serviço', 'Profissionais liberais que querem mais agendamentos'],
        ],
        'sites-institucionais' => [
            'icon' => 'layout',
            'title' => 'Sites institucionais',
            'summary' => 'Sites completos para apresentar a empresa, gerar autoridade e facilitar o contato comercial.',
            'lead' => 'Estruturamos sites institucionais com arquitetura clara, visual consistente e base técnica pronta para crescer.',
            'topics' => [
                ['title' => 'Arquitetura de informação', 'description' => 'Organizamos páginas, navegação e mensagens para o visitante entender rápido e decidir.'],
                ['title' => 'Identidade e credibilidade', 'description' => 'Direção visual e padrões de interface que fortalecem a percepção da sua marca.'],
                ['title' => 'Base para evolução', 'description' => 'Estrutura de conteúdo e componentes reaproveitáveis para novas páginas.'],
            ],
            'ideal_for' => ['Empresas que precisam de presença profissional', 'Clínicas, escritórios e consultórios', 'Negócios com site antigo ou lento'],
        ],
        'sistemas-web' => [
            'icon' => 'code',
            'title' => 'Sistemas web',
            'summary' => 'Painéis, áreas restritas e sistemas sob medida para organizar a operação do seu negócio.',
            'lead' => 'Construímos sistemas web sob medida, como painéis de gestão, cadastros, agendas e áreas de cliente, com segurança e manutenção simples.',
            'topics' => [
                ['title' => 'Processo antes do código', 'description' => 'Mapeamos como a sua operação funciona hoje para o sistema resolver o problema certo.'],
                ['title' => 'Segurança e LGPD', 'description' => 'Controle de acesso por perfil, registros de auditoria e cuidado com dados pessoais desde o início.'],
                ['title' => 'Entrega por etapas', 'description' => 'O sistema vai para o ar em fases, para você usar e validar cedo, sem esperar meses.'],
            ],
            'ideal_for' => ['Gestão de clientes, pacientes ou leads', 'Controle de estoque, vendas e agenda', 'Substituir planilhas e processos manuais'],
        ],
        'seo-e-conteudo' => [
            'icon' => 'search',
            'title' => 'SEO e conteúdo',
            'summary' => 'Ajustes técnicos e de conteúdo para aparecer no Google e atrair visitas qualificadas.',
            'lead' => 'Trabalho contínuo de SEO técnico e conteúdo para aumentar autoridade, alcance orgânico e qualidade do tráfego.',
            'topics' => [
                ['title' => 'Diagnóstico técnico', 'description' => 'Mapeamos performance, indexação, estrutura semântica e experiência da página.'],
                ['title' => 'Plano editorial', 'description' => 'Pautas definidas a partir do que o seu cliente pesquisa e da jornada de compra.'],
                ['title' => 'Ciclos de otimização', 'description' => 'Ajustes recorrentes guiados por posição no Google, cliques e taxa de conversão.'],
            ],
            'ideal_for' => ['Sites que não aparecem no Google', 'Negócios locais que querem ser encontrados', 'Quem quer depender menos de anúncios'],
        ],
        'suporte-e-manutencao' => [
            'icon' => 'shield',
            'title' => 'Suporte e manutenção',
            'summary' => 'Seu site ou sistema atualizado, seguro e no ar, com melhorias contínuas.',
            'lead' => 'Mantemos seu projeto atualizado, seguro e funcionando, com acompanhamento técnico e melhorias por prioridade.',
            'topics' => [
                ['title' => 'Correções e estabilidade', 'description' => 'Monitoramos incidentes, corrigimos falhas e reduzimos o impacto na sua operação.'],
                ['title' => 'Evolução por prioridade', 'description' => 'Ajustes definidos pelo impacto no negócio e na experiência de quem usa.'],
                ['title' => 'Segurança operacional', 'description' => 'Backups, atualizações e proteção para manter o ambiente confiável.'],
            ],
            'ideal_for' => ['Sites e sistemas já publicados', 'Projetos sem responsável técnico', 'Quem precisa de atualizações frequentes'],
        ],
    ],

    'process' => [
        ['title' => 'Descoberta e diagnóstico', 'description' => 'Entendemos objetivo, público e contexto técnico para definir escopo, prioridades e indicadores.'],
        ['title' => 'Design e desenvolvimento', 'description' => 'Interface, conteúdo e código entregues em blocos revisáveis, com sua validação a cada etapa.'],
        ['title' => 'Publicação e evolução', 'description' => 'Entramos no ar com revisão de performance, SEO e segurança, e seguimos melhorando com base em dados.'],
    ],

    'differentials' => [
        ['icon' => 'gauge', 'title' => 'Performance de verdade', 'description' => 'Páginas leves, imagens otimizadas e carregamento rápido no celular.'],
        ['icon' => 'lock', 'title' => 'Segurança e LGPD', 'description' => 'Boas práticas de segurança e respeito aos dados dos seus clientes desde o primeiro dia.'],
        ['icon' => 'chart', 'title' => 'Decisões com dados', 'description' => 'Medição de contatos e conversões para saber o que funciona.'],
        ['icon' => 'handshake', 'title' => 'Contato direto', 'description' => 'Você fala com quem projeta e desenvolve, sem intermediários.'],
    ],

    /*
    | Portfólio. "type": demo (modelo de demonstração) ou client (projeto de cliente).
    | Prefira projetos reais publicados, com autorização do cliente.
    */
    'portfolio' => [
        [
            'title' => 'Medicare',
            'segment' => 'Médicos',
            'category' => 'Landing page',
            'type' => 'demo',
            'summary' => 'Landing page para consultórios médicos, otimizada para SEO e campanhas no Google Ads, com foco em agendamentos online.',
            'url' => 'https://natalcode.com.br/medico/',
            'image' => '/images/portfolio/medicare.webp',
        ],
        [
            'title' => 'Emotion',
            'segment' => 'Psicólogos',
            'category' => 'Landing page',
            'type' => 'demo',
            'summary' => 'Visual acolhedor para psicólogos fortalecerem a autoridade profissional e transformarem visitas em pacientes agendados.',
            'url' => 'https://natalcode.com.br/psicologia/',
            'image' => '/images/portfolio/emotion.webp',
        ],
        [
            'title' => 'Allegra',
            'segment' => 'Pediatras',
            'category' => 'Landing page',
            'type' => 'demo',
            'summary' => 'Landing page para pediatras, com especialidades, credenciais e agendamento de consultas em destaque.',
            'url' => 'https://natalcode.com.br/pediatria/',
            'image' => '/images/portfolio/allegra.webp',
        ],
    ],

    // Depoimentos: apenas reais e autorizados. Com a lista vazia, a seção não aparece.
    // Formato: ['quote' => '', 'name' => '', 'role' => '', 'company' => '', 'photo' => '/images/clientes/nome.webp'],
    'testimonials' => [],

    'about' => [
        'lead' => 'A NatalCode nasceu em Natal para unir design e desenvolvimento em uma entrega digital mais eficiente: projetos que comunicam bem, funcionam rápido e continuam evoluindo depois de publicados.',
        'mission' => 'Ajudar empresas a vender mais e operar melhor por meio de produtos digitais bem construídos.',
        'values' => [
            ['title' => 'Clareza', 'description' => 'Escopo, prazos e riscos comunicados de forma objetiva em cada etapa.'],
            ['title' => 'Qualidade', 'description' => 'Código organizado, com foco em manutenção, segurança e performance.'],
            ['title' => 'Parceria', 'description' => 'Decisões alinhadas com o contexto real do negócio de cada cliente.'],
        ],
        'founder' => [
            'name' => 'Lúcio Lemos',
            'role' => 'Fundador e desenvolvedor líder',
            'photo' => null,
            'bio' => [
                'A NatalCode é liderada pelo seu fundador, que atua diretamente na estratégia e na execução técnica de cada projeto. Esse modelo garante alinhamento entre a visão de negócio e a qualidade do que é entregue.',
                'O trabalho conecta estratégia, design e engenharia em um processo disciplinado: diagnóstico e metas, implementação com padrão de qualidade e evolução contínua com base em dados.',
            ],
            'quote' => 'Um bom projeto digital nasce de contexto claro, execução disciplinada e melhoria constante.',
        ],
    ],

    'faq' => [
        ['question' => 'Que tipos de projeto a NatalCode atende?', 'answer' => 'Landing pages, sites institucionais, sistemas web sob medida, SEO e manutenção de sites e sistemas já publicados.'],
        ['question' => 'Posso contratar apenas uma landing page?', 'answer' => 'Sim. Projetos de página única fazem parte do nosso escopo e podem evoluir depois para um site completo.'],
        ['question' => 'Qual é o prazo médio de entrega?', 'answer' => 'Depende do escopo. Projetos enxutos ficam prontos em algumas semanas; estruturas maiores são entregues em fases, para você usar cedo.'],
        ['question' => 'Como funciona o início do projeto?', 'answer' => 'Começamos com uma conversa sobre contexto, objetivo e público. A partir dela, montamos um escopo inicial com prioridades, prazo e investimento.'],
        ['question' => 'Como acompanho o andamento?', 'answer' => 'Trabalhamos por etapas, com entregas que você vê e valida. Assim os ajustes acontecem cedo, e não só no final.'],
        ['question' => 'Vocês oferecem manutenção depois da publicação?', 'answer' => 'Sim. Temos acompanhamento técnico para correções, atualizações de segurança e melhorias contínuas.'],
        ['question' => 'A NatalCode cuida de SEO?', 'answer' => 'Sim. Todo projeto sai com SEO técnico básico, e oferecemos trabalho contínuo de conteúdo e otimização com base em dados de busca e conversão.'],
        ['question' => 'Quais tecnologias vocês usam?', 'answer' => 'Trabalhamos com PHP e Laravel, Tailwind CSS e JavaScript moderno, com foco em performance, segurança e manutenção simples.'],
        ['question' => 'Vocês atendem fora de Natal?', 'answer' => 'Sim. O atendimento é remoto para todo o Brasil, com reuniões por vídeo.'],
        ['question' => 'Como peço um orçamento?', 'answer' => 'Pela página de contato ou pelo WhatsApp. Conte seu objetivo e retornamos com os próximos passos.'],
    ],

    'cta' => [
        'title' => 'Vamos construir o próximo passo do seu negócio?',
        'lead' => 'Conte o que você precisa: lançar, melhorar a conversão ou organizar a operação online. Retornamos com um caminho claro e um orçamento sem compromisso.',
    ],

];
