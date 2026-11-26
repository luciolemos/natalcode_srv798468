@php
    $contact = config('site.contact');
    $updated = config('site.legal.privacy_updated_at');
    $hasAnalytics = (bool) config('site.analytics.ga4');
@endphp

<x-layouts.app title="Política de Privacidade" description="Como a NatalCode trata dados pessoais neste site: o que coletamos, para quê, com quem compartilhamos, cookies e como exercer seus direitos pela LGPD.">
    <x-page-hero kicker="LGPD" title="Política de Privacidade"
        lead="Como tratamos os dados pessoais de quem visita este site ou entra em contato com a NatalCode."
        :breadcrumbs="['Política de Privacidade' => null]" />

    <section class="section">
        <div class="container-site max-w-3xl">
            <p class="text-sm text-ink-500">Vigente desde {{ \Illuminate\Support\Carbon::parse($updated)->format('d/m/Y') }}.</p>

            <div class="legal-text mt-10">
                <h2>1. Quem somos</h2>
                <p>
                    A {{ config('site.legal_name') }}@if ($contact['cnpj']), CNPJ {{ $contact['cnpj'] }},@endif com atuação em {{ $contact['city'] }},
                    é a controladora dos dados pessoais tratados neste site, nos termos da Lei Geral de Proteção de Dados (Lei nº 13.709/2018).
                </p>

                <h2>2. Dados que tratamos</h2>
                <p>Este site não tem formulários de cadastro nem área de login. Tratamos apenas:</p>
                <ul>
                    <li><strong>Dados que você nos envia</strong> ao falar com a gente por WhatsApp ou e-mail: nome, telefone, e-mail e o conteúdo da mensagem.</li>
                    <li><strong>Registros técnicos do servidor</strong>: endereço IP, data e hora, página acessada e navegador, gerados automaticamente a cada acesso.</li>
                    <li><strong>Métricas de navegação</strong>, somente se você aceitar os cookies de medição: páginas visitadas, tempo de visita, tipo de dispositivo, localização aproximada e origem do acesso, coletados pelo Google Analytics.</li>
                </ul>

                <h2>3. Para que usamos e com qual base legal</h2>
                <ul>
                    <li><strong>Responder contatos e preparar propostas</strong>: procedimentos preliminares a contrato, a seu pedido (art. 7º, V).</li>
                    <li><strong>Executar os serviços contratados</strong>: execução de contrato (art. 7º, V).</li>
                    <li><strong>Manter o site seguro e funcionando</strong> (registros do servidor): legítimo interesse (art. 7º, IX).</li>
                    <li><strong>Entender como o site é usado e melhorá-lo</strong> (Google Analytics): consentimento (art. 7º, I), que você pode revogar a qualquer momento.</li>
                    <li><strong>Cumprir obrigações legais</strong>, como guardar registros de acesso e documentos fiscais: obrigação legal (art. 7º, II).</li>
                </ul>

                <h2>4. Compartilhamento</h2>
                <p>Não vendemos dados pessoais. Eles só chegam a terceiros quando isso é necessário para o site ou o serviço funcionar:</p>
                <ul>
                    <li><strong>Hospedagem</strong> (Hostinger), onde o site e os registros do servidor ficam armazenados;</li>
                    <li><strong>Google</strong>, pelo Google Analytics, apenas com o seu consentimento;</li>
                    <li><strong>WhatsApp (Meta)</strong> e o provedor de e-mail, quando você escolhe falar com a gente por esses canais. O uso deles também segue as políticas de privacidade de cada empresa;</li>
                    <li><strong>Autoridades públicas</strong>, quando houver obrigação legal ou ordem judicial.</li>
                </ul>
                <p>
                    Alguns desses fornecedores podem processar dados fora do Brasil. Nesses casos, a transferência segue o art. 33 da LGPD
                    e as garantias contratuais oferecidas por eles.
                </p>

                <h2>5. Cookies e armazenamento no navegador</h2>
                <p>
                    O site funciona sem cookies de rastreamento. Os cookies do Google Analytics (<code>_ga</code> e <code>_ga_*</code>) só são criados
                    se você clicar em <strong>Aceitar</strong> no aviso de privacidade. Ao clicar em <strong>Recusar</strong>, nada do Google é carregado.
                    Usamos o Analytics com anonimização de IP.
                </p>
                <p>
                    Para lembrar a sua escolha, guardamos no seu navegador (<code>localStorage</code>) a informação "aceito" ou "recusado". Não há
                    nenhum dado pessoal nesse registro.
                </p>
                @if ($hasAnalytics)
                    <p>
                        Você pode mudar de ideia quando quiser:
                        <button type="button" class="font-medium text-brand-600 underline underline-offset-2 hover:text-brand-700" data-consent-open>rever preferências de cookies</button>.
                        Também é possível apagar cookies e dados do site nas configurações do seu navegador.
                    </p>
                @endif

                <h2>6. Por quanto tempo guardamos</h2>
                <ul>
                    <li><strong>Conversas e propostas</strong>: enquanto durar a negociação ou o contrato e, depois, pelo prazo necessário para cumprir obrigações legais e exercer direitos em processos.</li>
                    <li><strong>Registros de acesso ao site</strong>: pelo menos 6 meses, como exige o Marco Civil da Internet (art. 15).</li>
                    <li><strong>Dados do Google Analytics</strong>: pelo prazo de retenção configurado na ferramenta, de no máximo 14 meses.</li>
                </ul>
                <p>Depois disso, os dados são eliminados ou anonimizados.</p>

                <h2>7. Seus direitos</h2>
                <p>
                    Pela LGPD (art. 18), você pode pedir: confirmação de que tratamos seus dados; acesso a eles; correção; anonimização, bloqueio ou eliminação
                    de dados desnecessários; portabilidade; informação sobre com quem compartilhamos; e revogação do consentimento. Também pode reclamar
                    à Autoridade Nacional de Proteção de Dados (ANPD).
                </p>

                <h2>8. Como falar com a gente sobre privacidade</h2>
                <p>
                    Envie o seu pedido para <a href="mailto:{{ $contact['email'] }}?subject={{ rawurlencode('Privacidade (LGPD)') }}">{{ $contact['email'] }}</a>.
                    Respondemos em até 15 dias e podemos pedir informações para confirmar a sua identidade antes de atender.
                </p>

                <h2>9. Segurança</h2>
                <p>
                    O site usa conexão criptografada (HTTPS) e cabeçalhos de segurança, e o acesso aos dados fica restrito a quem precisa deles
                    para trabalhar. Nenhum sistema é totalmente imune a incidentes. Se acontecer algum que possa causar risco ou dano relevante,
                    avisaremos os titulares afetados e a ANPD, como manda a lei.
                </p>

                <h2>10. Crianças e adolescentes</h2>
                <p>Este site e os nossos serviços são voltados a empresas e profissionais. Não coletamos, de propósito, dados de menores de 18 anos.</p>

                <h2>11. Mudanças nesta política</h2>
                <p>
                    Podemos atualizar esta política quando o site ou a legislação mudar. A versão vigente fica sempre nesta página, com a data no topo.
                </p>
            </div>
        </div>
    </section>
</x-layouts.app>
