@props(['kicker' => null, 'title', 'lead' => null, 'breadcrumbs' => []])

{{-- Topo das páginas internas: faixa escura com grade sutil. --}}
<section class="relative overflow-hidden bg-ink-950 text-white">
    <div class="bg-grid absolute inset-0 [mask-image:radial-gradient(ellipse_at_top,black,transparent_70%)]" aria-hidden="true"></div>
    <div class="absolute -top-40 left-1/2 h-80 w-[48rem] -translate-x-1/2 rounded-full bg-brand-600/30 blur-3xl" aria-hidden="true"></div>

    <div class="container-site relative py-16 sm:py-20">
        @if ($breadcrumbs)
            <nav aria-label="Você está em" class="mb-6 text-sm text-ink-400">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('home') }}" class="hover:text-white">Início</a></li>
                    @foreach ($breadcrumbs as $label => $url)
                        <li aria-hidden="true">/</li>
                        <li>
                            @if ($url)
                                <a href="{{ $url }}" class="hover:text-white">{{ $label }}</a>
                            @else
                                <span class="text-ink-200" aria-current="page">{{ $label }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif

        @if ($kicker)
            <p class="font-mono text-xs font-medium tracking-wide text-brand-300 uppercase">{{ $kicker }}</p>
        @endif
        <h1 class="mt-3 max-w-3xl text-4xl font-semibold text-white sm:text-5xl">{{ $title }}</h1>
        @if ($lead)
            <p class="mt-5 max-w-2xl text-lg leading-relaxed text-ink-300">{{ $lead }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
