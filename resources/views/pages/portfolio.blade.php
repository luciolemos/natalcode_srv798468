<x-layouts.app title="Portfólio" description="Modelos de landing pages e sites por segmento desenvolvidos pela NatalCode.">
    <x-page-hero kicker="Portfólio" title="Projetos e modelos por segmento"
        lead="Veja demonstrações navegáveis e escolha um ponto de partida. Cada modelo é adaptado à identidade e aos objetivos do seu negócio."
        :breadcrumbs="['Portfólio' => null]" />

    <section class="section">
        <div class="container-site grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach (config('site.portfolio') as $item)
                <x-portfolio-card :item="$item" />
            @endforeach
        </div>
    </section>

    <x-cta-band title="Seu negócio pode ser o próximo projeto" />
</x-layouts.app>
