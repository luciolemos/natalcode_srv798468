@props(['kicker' => null, 'title', 'lead' => null, 'align' => 'left', 'as' => 'h2'])

<div {{ $attributes->class(['max-w-2xl', 'mx-auto text-center' => $align === 'center']) }}>
    @if ($kicker)
        <p class="kicker">{{ $kicker }}</p>
    @endif
    <{{ $as }} class="mt-3 text-3xl font-semibold sm:text-4xl">{{ $title }}</{{ $as }}>
    @if ($lead)
        <p class="mt-4 text-lg leading-relaxed text-ink-600">{{ $lead }}</p>
    @endif
</div>
