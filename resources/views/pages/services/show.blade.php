@php
    $others = collect(config('site.services'))->except($slug);
@endphp

<x-layouts.app :title="$service['title']" :description="$service['lead']"
    :schema="[[
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $service['title'],
        'description' => $service['lead'],
        'provider' => ['@type' => 'Organization', 'name' => config('site.name'), 'url' => url('/')],
        'areaServed' => 'BR',
    ]]">
    <x-page-hero kicker="Serviços" :title="$service['title']" :lead="$service['lead']"
        :breadcrumbs="['Serviços' => route('services.index'), $service['title'] => null]">
        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('contact', ['assunto' => $service['title']]) }}" class="btn-primary px-6 py-3.5">Pedir orçamento <x-icon name="arrow-right" class="size-4" /></a>
            <a href="{{ \App\Support\Site::whatsappUrl('Olá! Vim pelo site da NatalCode e quero saber mais sobre '.mb_strtolower($service['title']).'.') }}" target="_blank" rel="noopener" class="btn-ghost-dark px-6 py-3.5">
                <x-icon name="whatsapp" class="size-4" /> Conversar no WhatsApp
            </a>
        </div>
    </x-page-hero>

    <section class="section">
        <div class="container-site grid gap-12 lg:grid-cols-[1.6fr_1fr]">
            <div>
                <x-section-heading kicker="O que está incluído" title="Como entregamos" />
                <ol class="mt-10 space-y-5">
                    @foreach ($service['topics'] as $topic)
                        <li class="flex gap-5 rounded-2xl border border-ink-100 p-6">
                            <span class="font-mono text-sm font-semibold text-brand-600">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <h3 class="text-lg font-semibold">{{ $topic['title'] }}</h3>
                                <p class="mt-1.5 leading-relaxed text-ink-600">{{ $topic['description'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
            <aside class="h-fit rounded-2xl bg-ink-50 p-7 ring-1 ring-ink-100 lg:sticky lg:top-24">
                <h2 class="font-sans text-base font-semibold">Indicado para</h2>
                <ul class="mt-4 space-y-3">
                    @foreach ($service['ideal_for'] as $item)
                        <li class="flex gap-2.5 text-[15px] text-ink-700">
                            <x-icon name="check" class="mt-0.5 size-5 shrink-0 text-brand-600" /> {{ $item }}
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('contact', ['assunto' => $service['title']]) }}" class="btn-primary mt-7 w-full">Quero este serviço</a>
            </aside>
        </div>
    </section>

    <section class="section bg-ink-50">
        <div class="container-site">
            <x-section-heading kicker="Outros serviços" title="Combine soluções conforme o momento do seu negócio" />
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($others as $otherSlug => $other)
                    <x-service-card :slug="$otherSlug" :service="$other" />
                @endforeach
            </div>
        </div>
    </section>

    <x-cta-band />
</x-layouts.app>
