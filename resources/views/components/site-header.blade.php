@php
    $nav = config('site.nav');
    $isActive = fn (string $route) => request()->routeIs($route) || request()->routeIs(\Illuminate\Support\Str::before($route, '.').'.*');
@endphp

<header class="sticky top-0 z-40 border-b border-ink-100 bg-white/85 backdrop-blur-md supports-[backdrop-filter]:bg-white/75" data-site-header>
    <div class="container-site flex h-16 items-center justify-between gap-6">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5" aria-label="{{ config('site.name') }}, página inicial">
            <img src="/images/brand/nc-icon.png" alt="" width="32" height="32" class="size-8">
            <span class="font-display text-lg font-semibold tracking-tight text-ink-950">NatalCode</span>
        </a>

        <nav class="hidden items-center gap-1 md:flex" aria-label="Principal">
            @foreach ($nav as $item)
                <a href="{{ route($item['route']) }}"
                   @class([
                       'rounded-full px-3.5 py-2 text-sm font-medium transition',
                       'text-brand-700 bg-brand-50' => $isActive($item['route']),
                       'text-ink-600 hover:text-ink-950 hover:bg-ink-50' => ! $isActive($item['route']),
                   ])
                   @if ($isActive($item['route'])) aria-current="page" @endif>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ route('contact') }}" class="btn-primary hidden py-2.5 sm:inline-flex">Fale conosco</a>
            <button type="button" class="inline-flex size-10 items-center justify-center rounded-full text-ink-800 hover:bg-ink-50 md:hidden"
                    aria-controls="menu-mobile" aria-expanded="false" data-menu-toggle>
                <span class="sr-only">Abrir menu</span>
                <x-icon name="menu" class="size-6" data-menu-icon-open />
                <x-icon name="x" class="hidden size-6" data-menu-icon-close />
            </button>
        </div>
    </div>

    <nav id="menu-mobile" class="hidden border-t border-ink-100 bg-white md:hidden" aria-label="Principal (celular)" data-menu>
        <div class="container-site flex flex-col gap-1 py-4">
            @foreach ($nav as $item)
                <a href="{{ route($item['route']) }}"
                   @class([
                       'rounded-xl px-4 py-3 text-base font-medium',
                       'bg-brand-50 text-brand-700' => $isActive($item['route']),
                       'text-ink-800 hover:bg-ink-50' => ! $isActive($item['route']),
                   ])>
                    {{ $item['label'] }}
                </a>
            @endforeach
            <a href="{{ route('contact') }}" class="btn-primary mt-2">Fale conosco</a>
        </div>
    </nav>
</header>
