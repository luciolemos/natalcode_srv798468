@php
    $faq = config('site.faq');
@endphp

<x-layouts.app title="Dúvidas frequentes" description="Prazos, formas de trabalho, manutenção, SEO e tecnologias: respostas para as dúvidas mais comuns sobre os projetos da NatalCode."
    :schema="[[
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(fn ($item) => [
            '@type' => 'Question',
            'name' => $item['question'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['answer']],
        ], $faq),
    ]]">
    <x-page-hero kicker="Dúvidas" title="Perguntas frequentes"
        lead="Não encontrou o que procurava? Fale com a gente pelo WhatsApp ou pela página de contato."
        :breadcrumbs="['Dúvidas' => null]" />

    <section class="section">
        <div class="container-site max-w-3xl">
            <x-faq-list :items="$faq" />
        </div>
    </section>

    <x-cta-band title="Ainda tem alguma dúvida?" lead="Respondemos rápido. Conte o que você precisa e indicamos o melhor caminho." />
</x-layouts.app>
