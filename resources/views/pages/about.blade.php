@php
    $about = config('site.about');
    $founder = $about['founder'];
@endphp

<x-layouts.app title="Sobre" description="Conheça a NatalCode: agência de Natal/RN que cria sites e sistemas web com estratégia, design e engenharia.">
    <x-page-hero kicker="Sobre a NatalCode" title="Design e desenvolvimento a serviço do seu negócio" :lead="$about['lead']"
        :breadcrumbs="['Sobre' => null]" />

    <section class="section">
        <div class="container-site grid gap-12 lg:grid-cols-[1fr_1.4fr] lg:items-start">
            <div>
                <p class="kicker">Missão</p>
                <p class="mt-4 font-display text-2xl leading-snug font-medium text-ink-950 sm:text-3xl">{{ $about['mission'] }}</p>
            </div>
            <ul class="grid gap-5 sm:grid-cols-3 lg:grid-cols-1 xl:grid-cols-3">
                @foreach ($about['values'] as $value)
                    <li class="rounded-2xl border border-ink-100 p-6">
                        <h2 class="font-sans text-base font-semibold text-ink-950">{{ $value['title'] }}</h2>
                        <p class="mt-2 text-[15px] leading-relaxed text-ink-600">{{ $value['description'] }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section bg-ink-50">
        <div class="container-site grid gap-12 lg:grid-cols-[auto_1fr] lg:items-center">
            <div class="mx-auto flex size-56 items-center justify-center overflow-hidden rounded-3xl bg-ink-950 ring-1 ring-ink-200 sm:size-64">
                @if ($founder['photo'])
                    <img src="{{ $founder['photo'] }}" alt="Foto de {{ $founder['name'] }}" class="size-full object-cover" loading="lazy">
                @else
                    <img src="/images/brand/nc-icon.png" alt="" class="size-28 invert">
                @endif
            </div>
            <div>
                <p class="kicker">Quem conduz</p>
                <h2 class="mt-3 text-3xl font-semibold">{{ $founder['name'] }}</h2>
                <p class="mt-1 text-ink-500">{{ $founder['role'] }}</p>
                <div class="prose-site mt-6 max-w-2xl leading-relaxed text-ink-700">
                    @foreach ($founder['bio'] as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
                <blockquote class="mt-8 border-l-2 border-brand-500 pl-5 font-display text-lg text-ink-950">“{{ $founder['quote'] }}”</blockquote>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container-site">
            <x-section-heading kicker="Como trabalhamos" title="Processo claro do começo ao fim" align="center" />
            <ol class="mt-12 grid gap-6 md:grid-cols-3">
                @foreach (config('site.process') as $step)
                    <li class="rounded-2xl border border-ink-100 p-7">
                        <span class="font-mono text-sm font-semibold text-brand-600">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="mt-3 text-lg font-semibold">{{ $step['title'] }}</h3>
                        <p class="mt-2 leading-relaxed text-ink-600">{{ $step['description'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <x-cta-band />
</x-layouts.app>
