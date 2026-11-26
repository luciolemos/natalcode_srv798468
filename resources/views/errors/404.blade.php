<x-layouts.app title="Página não encontrada" :noindex="true">
    <section class="section">
        <div class="container-site max-w-2xl text-center">
            <p class="font-mono text-sm font-semibold text-brand-600">404</p>
            <h1 class="mt-3 text-4xl font-semibold sm:text-5xl">Página não encontrada</h1>
            <p class="mt-5 text-lg text-ink-600">O endereço pode ter mudado ou não existe mais. Que tal começar por aqui?</p>
            <div class="mt-9 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('home') }}" class="btn-primary">Ir para o início</a>
                <a href="{{ route('services.index') }}" class="btn-ghost">Ver serviços</a>
                <a href="{{ route('contact') }}" class="btn-ghost">Falar com a gente</a>
            </div>
        </div>
    </section>
</x-layouts.app>
