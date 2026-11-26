@php
    $services = config('site.services');
    $social = config('site.social');
    $contact = config('site.contact');
@endphp

<footer class="bg-ink-950 text-ink-300">
    <div class="container-site grid gap-12 py-16 md:grid-cols-12">
        <div class="md:col-span-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5" aria-label="{{ config('site.name') }}, página inicial">
                <img src="/images/brand/nc-icon.png" alt="" width="32" height="32" class="size-8 invert">
                <span class="font-display text-lg font-semibold text-white">NatalCode</span>
            </a>
            <p class="mt-4 max-w-xs text-sm leading-relaxed text-ink-400">{{ config('site.tagline') }}</p>
            <ul class="mt-6 flex gap-2" aria-label="Redes sociais">
                @foreach ($social as $key => $item)
                    <li>
                        <a href="{{ $item['url'] }}" target="_blank" rel="noopener" class="inline-flex size-10 items-center justify-center rounded-full bg-white/5 text-ink-300 transition hover:bg-white/10 hover:text-white">
                            <x-icon :name="$key" class="size-[18px]" />
                            <span class="sr-only">{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="md:col-span-3">
            <h2 class="font-sans text-sm font-semibold text-white">Serviços</h2>
            <ul class="mt-4 space-y-2.5 text-sm">
                @foreach ($services as $slug => $service)
                    <li><a href="{{ route('services.show', $slug) }}" class="transition hover:text-white">{{ $service['title'] }}</a></li>
                @endforeach
            </ul>
        </div>

        <div class="md:col-span-2">
            <h2 class="font-sans text-sm font-semibold text-white">NatalCode</h2>
            <ul class="mt-4 space-y-2.5 text-sm">
                <li><a href="{{ route('portfolio') }}" class="transition hover:text-white">Portfólio</a></li>
                <li><a href="{{ route('about') }}" class="transition hover:text-white">Sobre</a></li>
                <li><a href="{{ route('faq') }}" class="transition hover:text-white">Dúvidas</a></li>
                <li><a href="{{ route('contact') }}" class="transition hover:text-white">Contato</a></li>
            </ul>
        </div>

        <div class="md:col-span-3">
            <h2 class="font-sans text-sm font-semibold text-white">Contato</h2>
            <ul class="mt-4 space-y-3 text-sm">
                <li>
                    <a href="{{ \App\Support\Site::whatsappUrl() }}" target="_blank" rel="noopener" class="flex items-center gap-2.5 transition hover:text-white">
                        <x-icon name="whatsapp" class="size-4 shrink-0" /> WhatsApp
                    </a>
                </li>
                <li>
                    <a href="mailto:{{ $contact['email'] }}" class="flex items-center gap-2.5 transition hover:text-white">
                        <x-icon name="mail" class="size-4 shrink-0" /> {{ $contact['email'] }}
                    </a>
                </li>
                <li class="flex items-center gap-2.5">
                    <x-icon name="map-pin" class="size-4 shrink-0" /> {{ $contact['city'] }} · {{ $contact['coverage'] }}
                </li>
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="container-site flex flex-col gap-3 py-6 text-xs text-ink-400 sm:flex-row sm:items-center sm:justify-between">
            <p>
                © {{ now()->year }} {{ config('site.legal_name') }}
                @if ($contact['cnpj']) · CNPJ {{ $contact['cnpj'] }} @endif
            </p>
            <ul class="flex flex-wrap gap-x-5 gap-y-2">
                @if (Route::has('privacy'))
                    <li><a href="{{ route('privacy') }}" class="hover:text-white">Política de Privacidade</a></li>
                @endif
                @if (Route::has('terms'))
                    <li><a href="{{ route('terms') }}" class="hover:text-white">Termos de Uso</a></li>
                @endif
                @if (config('site.analytics.ga4'))
                    <li><button type="button" class="hover:text-white" data-consent-open>Preferências de cookies</button></li>
                @endif
            </ul>
        </div>
    </div>
</footer>
