{{-- Banner de consentimento (LGPD). Controlado por resources/js/analytics.js. --}}
<div class="fixed inset-x-3 bottom-3 z-50 mx-auto max-w-3xl rounded-2xl bg-ink-950 p-5 text-sm text-ink-200 shadow-2xl ring-1 ring-white/10 sm:flex sm:items-center sm:gap-6"
     role="dialog" aria-live="polite" aria-label="Preferências de privacidade" data-consent hidden>
    <p class="leading-relaxed">
        Usamos cookies de medição (Google Analytics) para entender como o site é usado e melhorar nossos serviços.
        Eles só são ativados se você permitir.
        @if (Route::has('privacy'))
            <a href="{{ route('privacy') }}" class="font-medium text-white underline underline-offset-2">Política de Privacidade</a>
        @endif
    </p>
    <div class="mt-4 flex shrink-0 gap-2 sm:mt-0">
        <button type="button" class="btn-ghost-dark flex-1 py-2.5" data-consent-choice="denied">Recusar</button>
        <button type="button" class="btn-light flex-1 py-2.5" data-consent-choice="granted">Aceitar</button>
    </div>
</div>
