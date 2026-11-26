@php
    $contact = config('site.contact');
    $subject = trim((string) request()->query('assunto', ''));
    $message = $subject !== ''
        ? 'Olá! Vim pelo site da NatalCode e quero conversar sobre: '.\Illuminate\Support\Str::limit($subject, 80, '').'.'
        : null;
@endphp

<x-layouts.app title="Contato" description="Fale com a NatalCode pelo WhatsApp ou e-mail. Atendimento remoto para todo o Brasil.">
    <x-page-hero kicker="Contato" title="Vamos conversar sobre o seu projeto"
        lead="Conte seu objetivo e retornamos com os próximos passos, um escopo inicial e uma estimativa sem compromisso."
        :breadcrumbs="['Contato' => null]" />

    <section class="section">
        <div class="container-site grid gap-6 md:grid-cols-2">
            <a href="{{ \App\Support\Site::whatsappUrl($message) }}" target="_blank" rel="noopener"
               class="group flex flex-col rounded-2xl bg-ink-950 p-8 text-white transition hover:-translate-y-0.5 hover:shadow-xl hover:shadow-ink-900/20">
                <span class="inline-flex size-12 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-400"><x-icon name="whatsapp" class="size-6" /></span>
                <h2 class="mt-6 text-2xl font-semibold text-white">WhatsApp</h2>
                <p class="mt-2 flex-1 leading-relaxed text-ink-300">O caminho mais rápido. Respondemos em horário comercial.</p>
                <span class="mt-6 inline-flex items-center gap-1.5 font-semibold text-emerald-400">Iniciar conversa <x-icon name="arrow-right" class="size-4 transition group-hover:translate-x-0.5" /></span>
            </a>
            <a href="mailto:{{ $contact['email'] }}{{ $subject !== '' ? '?subject='.rawurlencode($subject) : '' }}"
               class="group flex flex-col rounded-2xl border border-ink-100 p-8 transition hover:-translate-y-0.5 hover:border-brand-200 hover:shadow-xl hover:shadow-brand-900/5">
                <span class="inline-flex size-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 ring-1 ring-brand-100"><x-icon name="mail" class="size-6" /></span>
                <h2 class="mt-6 text-2xl font-semibold">E-mail</h2>
                <p class="mt-2 flex-1 leading-relaxed text-ink-600">Para enviar briefing, referências ou documentos.</p>
                <span class="mt-6 inline-flex items-center gap-1.5 font-semibold text-brand-700">{{ $contact['email'] }} <x-icon name="arrow-right" class="size-4 transition group-hover:translate-x-0.5" /></span>
            </a>
        </div>
        <p class="container-site mt-8 flex items-center gap-2 text-sm text-ink-500">
            <x-icon name="map-pin" class="size-4" /> {{ $contact['city'] }} · {{ $contact['coverage'] }}
        </p>
    </section>

    <section class="section bg-ink-50">
        <div class="container-site max-w-3xl">
            <x-section-heading kicker="Antes de falar com a gente" title="Dúvidas comuns" align="center" />
            <x-faq-list class="mt-10" :items="array_slice(config('site.faq'), 1, 4)" />
        </div>
    </section>
</x-layouts.app>
