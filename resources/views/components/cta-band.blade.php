@props(['title' => null, 'lead' => null])

<section class="section">
    <div class="container-site">
        <div class="relative overflow-hidden rounded-3xl bg-ink-950 px-6 py-14 text-center sm:px-12 sm:py-20">
            <div class="bg-grid absolute inset-0 [mask-image:radial-gradient(ellipse_at_center,black,transparent_75%)]" aria-hidden="true"></div>
            <div class="absolute -bottom-32 left-1/2 h-64 w-[40rem] -translate-x-1/2 rounded-full bg-brand-600/40 blur-3xl" aria-hidden="true"></div>
            <div class="relative mx-auto max-w-2xl">
                <h2 class="text-3xl font-semibold text-white sm:text-4xl">{{ $title ?? config('site.cta.title') }}</h2>
                <p class="mt-4 text-lg leading-relaxed text-ink-300">{{ $lead ?? config('site.cta.lead') }}</p>
                <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                    <a href="{{ route('contact') }}" class="btn-light">Solicitar orçamento <x-icon name="arrow-right" class="size-4" /></a>
                    <a href="{{ \App\Support\Site::whatsappUrl() }}" target="_blank" rel="noopener" class="btn-ghost-dark">
                        <x-icon name="whatsapp" class="size-4" /> Conversar no WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
