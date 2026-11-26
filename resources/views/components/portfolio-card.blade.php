@props(['item'])

<article class="group flex flex-col overflow-hidden rounded-2xl border border-ink-100 bg-white transition hover:shadow-xl hover:shadow-brand-900/5">
    <div class="relative aspect-[16/9] overflow-hidden bg-ink-100">
        <img src="{{ $item['image'] }}" alt="Prévia do projeto {{ $item['title'] }}" width="1200" height="675" loading="lazy" decoding="async"
             class="size-full object-cover object-top transition duration-500 group-hover:scale-[1.03]">
        <span class="absolute top-3 left-3 rounded-full bg-white/90 px-2.5 py-1 text-xs font-semibold text-ink-800 backdrop-blur">
            {{ $item['type'] === 'client' ? 'Cliente' : 'Modelo' }} · {{ $item['category'] }}
        </span>
    </div>
    <div class="flex flex-1 flex-col p-6">
        <p class="kicker">{{ $item['segment'] }}</p>
        <h3 class="mt-2 text-xl font-semibold">{{ $item['title'] }}</h3>
        <p class="mt-2 flex-1 text-[15px] leading-relaxed text-ink-600">{{ $item['summary'] }}</p>
        <div class="mt-5 flex flex-wrap gap-2">
            <a href="{{ $item['url'] }}" target="_blank" rel="noopener" class="btn-ghost py-2.5">
                {{ $item['type'] === 'client' ? 'Visitar site' : 'Ver demonstração' }} <x-icon name="external" class="size-4" />
            </a>
            <a href="{{ route('contact', ['assunto' => $item['category'].' para '.$item['segment']]) }}" class="btn py-2.5 text-brand-700 hover:bg-brand-50">Quero um assim</a>
        </div>
    </div>
</article>
