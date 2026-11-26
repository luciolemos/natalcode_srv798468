@props(['slug', 'service'])

<a href="{{ route('services.show', $slug) }}"
   class="group relative flex flex-col rounded-2xl border border-ink-100 bg-white p-6 transition hover:-translate-y-0.5 hover:border-brand-200 hover:shadow-xl hover:shadow-brand-900/5">
    <span class="inline-flex size-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600 ring-1 ring-brand-100 transition group-hover:bg-brand-600 group-hover:text-white">
        <x-icon :name="$service['icon']" class="size-[22px]" />
    </span>
    <h3 class="mt-5 text-lg font-semibold">{{ $service['title'] }}</h3>
    <p class="mt-2 flex-1 text-[15px] leading-relaxed text-ink-600">{{ $service['summary'] }}</p>
    <span class="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700">
        Saiba mais <x-icon name="arrow-right" class="size-4 transition group-hover:translate-x-0.5" />
    </span>
</a>
