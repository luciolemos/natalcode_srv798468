# Conteudo da Home (NatalCode)

Este diretório centraliza os textos e listas da página inicial.

## Arquivo principal

- `app/content/home.php`

## Como editar

Edite apenas os valores de texto e links dentro dos arrays. Evite mudar os nomes das chaves.

### Blocos de seção (titulo/subtitulo)

Em `sections`:

- `hero`
- `features`
- `socialProof`
- `roadmap`
- `faq`
- `cta`

Cada bloco usa chaves como:

- `kicker`
- `title`
- `lead`

## Listas de itens

No mesmo arquivo `home.php`:

- `heroActions`: botoes do Hero
- `heroMetrics`: indicadores do Hero
- `featuresItems`: cards de Solucoes
- `socialProofTrustCards`: cards de confianca
- `socialProofTestimonials`: depoimentos
- `roadmapItems`: passos do Roadmap
- `faqItems`: perguntas e respostas
- `ctaActions`: botoes da chamada final

## Ordem de exibicao

A ordem na tela segue a ordem dos itens no array.

## Delays de animacao

Campos `delay` controlam o tempo do AOS. Mantenha valores progressivos para um efeito fluido.

## Boas praticas

- Mantenha textos curtos e objetivos.
- Preserve a consistencia da linha editorial.
- Ao trocar links, valide se o destino existe.
- Nao remova chaves usadas pelos templates.

## Onde isso e renderizado

Templates que consomem esses dados:

- `templates/home/hero.twig`
- `templates/home/features.twig`
- `templates/home/social-proof.twig`
- `templates/home/roadmap.twig`
- `templates/home/faq.twig`
- `templates/home/final-cta.twig`
