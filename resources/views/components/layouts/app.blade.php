@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'noindex' => false,
    'schema' => [],
])

@php
    $siteName = config('site.name');
    $pageTitle = $title ? $title.' | '.$siteName : $siteName.' | '.rtrim(config('site.tagline'), '.');
    $pageDescription = $description ?: config('site.description');
    $canonical = url()->current();
    $shareImage = url($image ?: config('site.share_image'));
    $schemas = array_merge([\App\Support\Site::organizationSchema()], $schema);
    $ga4 = config('site.analytics.ga4');
@endphp

<!DOCTYPE html>
<html lang="pt-BR" class="scroll-pt-24">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ $canonical }}">
    @if ($noindex)
        <meta name="robots" content="noindex, nofollow">
    @endif

    <meta property="og:type" content="website">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $shareImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="/favicon.ico" sizes="48x48">
    <link rel="icon" href="/favicon.png" type="image/png">
    <link rel="apple-touch-icon" href="/images/brand/nc-icon.png">
    <meta name="theme-color" content="#0b1020">

    @foreach ($schemas as $item)
        <script type="application/ld+json">{!! json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    @endforeach

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-dvh flex-col" @if ($ga4) data-ga4-id="{{ $ga4 }}" @endif>
    <a href="#conteudo" class="sr-only z-50 rounded-md bg-white px-4 py-2 text-sm font-semibold text-ink-950 focus:not-sr-only focus:fixed focus:top-3 focus:left-3">Pular para o conteúdo</a>

    <x-site-header />

    <main id="conteudo" class="flex-1">
        {{ $slot }}
    </main>

    <x-site-footer />

    @if ($ga4)
        <x-consent-banner />
    @endif
</body>
</html>
