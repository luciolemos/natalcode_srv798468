<x-layouts.app title="Serviços" description="Landing pages, sites institucionais, sistemas web, SEO e manutenção. Conheça os serviços da NatalCode.">
    <x-page-hero kicker="Serviços" title="Soluções digitais para cada fase do seu negócio"
        lead="Da primeira página de campanha ao sistema que organiza a sua operação, com o mesmo cuidado com performance, segurança e resultado."
        :breadcrumbs="['Serviços' => null]" />

    <section class="section">
        <div class="container-site grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach (config('site.services') as $slug => $service)
                <x-service-card :slug="$slug" :service="$service" />
            @endforeach
        </div>
    </section>

    <section class="section bg-ink-50 pt-0 sm:pt-0">
        <div class="container-site pt-20 sm:pt-28">
            <x-section-heading kicker="Como trabalhamos" title="O mesmo processo em todos os serviços" align="center" />
            <ol class="mt-12 grid gap-6 md:grid-cols-3">
                @foreach (config('site.process') as $step)
                    <li class="rounded-2xl bg-white p-7 ring-1 ring-ink-100">
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
