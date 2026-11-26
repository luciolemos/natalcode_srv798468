@php
    $hero = config('site.hero');
    $services = config('site.services');
    $testimonials = config('site.testimonials');
@endphp

<x-layouts.app>
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-ink-950 text-white">
        <div class="bg-grid absolute inset-0 [mask-image:radial-gradient(ellipse_at_top_right,black,transparent_70%)]" aria-hidden="true"></div>
        <div class="absolute -top-32 right-0 h-[28rem] w-[40rem] rounded-full bg-brand-600/35 blur-3xl" aria-hidden="true"></div>
        <div class="absolute -bottom-40 -left-20 h-80 w-96 rounded-full bg-sun-400/10 blur-3xl" aria-hidden="true"></div>

        <div class="container-site relative grid items-center gap-14 py-20 sm:py-24 lg:grid-cols-[1.05fr_1fr] lg:py-28">
            <div>
                <p class="inline-flex items-center gap-2 rounded-full bg-white/5 px-3 py-1.5 font-mono text-xs text-brand-200 ring-1 ring-white/10">
                    <span class="size-1.5 rounded-full bg-sun-400" aria-hidden="true"></span>
                    <span><span class="text-ink-400">// </span>{{ $hero['kicker'] }}</span>
                </p>
                <h1 class="mt-6 text-4xl leading-[1.08] font-semibold text-white sm:text-5xl lg:text-6xl">
                    {{ $hero['title'] }}
                </h1>
                <p class="mt-6 max-w-xl text-lg leading-relaxed text-ink-300">{{ $hero['lead'] }}</p>

                <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route($hero['primary']['route']) }}" class="btn-primary px-6 py-3.5">
                        {{ $hero['primary']['label'] }} <x-icon name="arrow-right" class="size-4" />
                    </a>
                    <a href="{{ route($hero['secondary']['route']) }}" class="btn-ghost-dark px-6 py-3.5">{{ $hero['secondary']['label'] }}</a>
                </div>

                <ul class="mt-10 grid gap-3 text-sm text-ink-300 sm:grid-cols-3 sm:gap-4">
                    @foreach ($hero['highlights'] as $highlight)
                        <li class="flex gap-2">
                            <x-icon name="check" class="mt-0.5 size-4 shrink-0 text-brand-300" />
                            <span>{{ $highlight }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Ilustração: um site sendo montado (decorativa). --}}
            <div class="relative mx-auto w-full max-w-lg lg:max-w-none" aria-hidden="true">
                <div class="rounded-2xl bg-white/5 p-2 ring-1 ring-white/10 backdrop-blur">
                    <div class="overflow-hidden rounded-xl bg-white shadow-2xl shadow-black/40">
                        <div class="flex items-center gap-1.5 border-b border-ink-100 bg-ink-50 px-4 py-3">
                            <span class="size-2.5 rounded-full bg-ink-200"></span>
                            <span class="size-2.5 rounded-full bg-ink-200"></span>
                            <span class="size-2.5 rounded-full bg-ink-200"></span>
                            <span class="ml-3 flex-1 truncate rounded-md bg-white px-3 py-1 font-mono text-[11px] text-ink-400 ring-1 ring-ink-100">seunegocio.com.br</span>
                        </div>
                        <div class="space-y-5 p-6">
                            <div class="flex items-center justify-between">
                                <div class="h-3 w-20 rounded-full bg-ink-900"></div>
                                <div class="flex gap-2">
                                    <div class="h-2 w-10 rounded-full bg-ink-200"></div>
                                    <div class="h-2 w-10 rounded-full bg-ink-200"></div>
                                    <div class="h-2 w-12 rounded-full bg-brand-500"></div>
                                </div>
                            </div>
                            <div class="space-y-2.5 pt-3">
                                <div class="h-5 w-4/5 rounded-md bg-ink-900"></div>
                                <div class="h-5 w-3/5 rounded-md bg-ink-900"></div>
                                <div class="h-2.5 w-11/12 rounded-full bg-ink-200"></div>
                                <div class="h-2.5 w-3/4 rounded-full bg-ink-200"></div>
                            </div>
                            <div class="flex gap-2.5">
                                <div class="h-9 w-32 rounded-full bg-brand-600"></div>
                                <div class="h-9 w-24 rounded-full ring-1 ring-ink-200"></div>
                            </div>
                            <div class="grid grid-cols-3 gap-3 pt-2">
                                <div class="h-20 rounded-lg bg-brand-50 ring-1 ring-brand-100"></div>
                                <div class="h-20 rounded-lg bg-ink-50 ring-1 ring-ink-100"></div>
                                <div class="h-20 rounded-lg bg-ink-50 ring-1 ring-ink-100"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="absolute -bottom-6 -left-4 flex items-center gap-3 rounded-xl bg-white px-4 py-3 text-ink-900 shadow-xl shadow-black/30 sm:-left-8">
                    <span class="inline-flex size-9 items-center justify-center rounded-full bg-emerald-100 text-emerald-700"><x-icon name="whatsapp" class="size-5" /></span>
                    <div class="text-left">
                        <p class="text-xs font-semibold">Novo contato pelo site</p>
                        <p class="text-[11px] text-ink-500">"Quero um orçamento..."</p>
                    </div>
                </div>

                <div class="absolute -top-5 -right-3 hidden rounded-xl bg-ink-900 px-4 py-3 font-mono text-[11px] leading-relaxed text-ink-300 shadow-xl ring-1 ring-white/10 sm:block">
                    <span class="text-brand-300">deploy</span> <span class="text-ink-500">→</span> <span class="text-emerald-400">no ar</span><br>
                    <span class="text-ink-500">mobile · seo · https</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Serviços --}}
    <section class="section" id="servicos">
        <div class="container-site">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <x-section-heading kicker="O que fazemos" title="Do primeiro site ao sistema sob medida"
                    lead="Escolha o ponto de partida. Cada projeto é planejado para o seu objetivo, não adaptado de um template." />
                <a href="{{ route('services.index') }}" class="btn-ghost shrink-0">Todos os serviços</a>
            </div>
            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($services as $slug => $service)
                    <x-service-card :slug="$slug" :service="$service" />
                @endforeach
                <a href="{{ route('contact') }}" class="flex flex-col justify-center rounded-2xl border border-dashed border-ink-200 p-6 text-center transition hover:border-brand-300 hover:bg-brand-50/40">
                    <x-icon name="sparkles" class="mx-auto size-7 text-brand-500" />
                    <p class="mt-3 font-semibold text-ink-950">Não sabe por onde começar?</p>
                    <p class="mt-1 text-sm text-ink-600">Conte seu objetivo e indicamos o melhor caminho.</p>
                </a>
            </div>
        </div>
    </section>

    {{-- Processo --}}
    <section class="section bg-ink-50" id="processo">
        <div class="container-site">
            <x-section-heading kicker="Como trabalhamos" title="Um processo simples, com você no controle"
                lead="Etapas curtas e entregas que você vê e valida. Sem surpresas no final." align="center" />
            <ol class="mt-14 grid gap-6 md:grid-cols-3">
                @foreach (config('site.process') as $step)
                    <li class="relative rounded-2xl bg-white p-7 ring-1 ring-ink-100">
                        <span class="font-mono text-sm font-semibold text-brand-600">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="mt-3 text-lg font-semibold">{{ $step['title'] }}</h3>
                        <p class="mt-2 leading-relaxed text-ink-600">{{ $step['description'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Portfólio --}}
    <section class="section" id="portfolio">
        <div class="container-site">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <x-section-heading kicker="Portfólio" title="Projetos para inspirar o seu"
                    lead="Modelos por segmento, prontos para virar o site do seu negócio." />
                <a href="{{ route('portfolio') }}" class="btn-ghost shrink-0">Ver portfólio</a>
            </div>
            <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach (array_slice(config('site.portfolio'), 0, 3) as $item)
                    <x-portfolio-card :item="$item" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Diferenciais --}}
    <section class="section bg-ink-950 text-white">
        <div class="container-site grid gap-12 lg:grid-cols-[1fr_1.3fr] lg:items-center">
            <div>
                <p class="font-mono text-xs font-medium tracking-wide text-brand-300 uppercase">Por que a NatalCode</p>
                <h2 class="mt-3 text-3xl font-semibold text-white sm:text-4xl">Feito para funcionar bem hoje e continuar evoluindo amanhã</h2>
                <p class="mt-4 text-lg leading-relaxed text-ink-300">Um site bonito não basta: ele precisa carregar rápido, aparecer no Google, proteger os dados de quem visita e gerar contatos que você consegue medir.</p>
            </div>
            <ul class="grid gap-4 sm:grid-cols-2">
                @foreach (config('site.differentials') as $item)
                    <li class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10">
                        <x-icon :name="$item['icon']" class="size-6 text-brand-300" />
                        <h3 class="mt-4 font-semibold text-white">{{ $item['title'] }}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-ink-300">{{ $item['description'] }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- Depoimentos (somente reais; a seção some com a lista vazia) --}}
    @if ($testimonials)
        <section class="section">
            <div class="container-site">
                <x-section-heading kicker="Clientes" title="Quem já trabalhou com a gente" align="center" />
                <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($testimonials as $testimonial)
                        <figure class="rounded-2xl border border-ink-100 p-7">
                            <blockquote class="leading-relaxed text-ink-700">“{{ $testimonial['quote'] }}”</blockquote>
                            <figcaption class="mt-5 flex items-center gap-3">
                                @if (! empty($testimonial['photo']))
                                    <img src="{{ $testimonial['photo'] }}" alt="" class="size-10 rounded-full object-cover" loading="lazy">
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-ink-950">{{ $testimonial['name'] }}</p>
                                    <p class="text-xs text-ink-500">{{ $testimonial['role'] }}{{ ! empty($testimonial['company']) ? ', '.$testimonial['company'] : '' }}</p>
                                </div>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Dúvidas --}}
    <section class="section">
        <div class="container-site grid gap-12 lg:grid-cols-[1fr_1.6fr]">
            <div>
                <x-section-heading kicker="Dúvidas" title="Perguntas frequentes" lead="O que costumam nos perguntar antes de começar um projeto." />
                <a href="{{ route('faq') }}" class="btn-ghost mt-8">Ver todas as dúvidas</a>
            </div>
            <x-faq-list :items="array_slice(config('site.faq'), 0, 5)" />
        </div>
    </section>

    <x-cta-band />
</x-layouts.app>
