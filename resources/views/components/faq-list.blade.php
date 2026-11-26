@props(['items'])

<div {{ $attributes->class(['divide-y divide-ink-100 rounded-2xl border border-ink-100 bg-white']) }}>
    @foreach ($items as $item)
        <details class="group px-6 [&_summary::-webkit-details-marker]:hidden" @if ($loop->first) open @endif>
            <summary class="flex cursor-pointer items-center justify-between gap-6 py-5 text-left font-semibold text-ink-950">
                {{ $item['question'] }}
                <x-icon name="chevron-down" class="size-5 shrink-0 text-ink-400 transition group-open:rotate-180" />
            </summary>
            <p class="-mt-1 pb-5 leading-relaxed text-ink-600">{{ $item['answer'] }}</p>
        </details>
    @endforeach
</div>
