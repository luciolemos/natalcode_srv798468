# NatalCode — site institucional (Laravel)

Site da NatalCode, agência de sites e sistemas web de Natal/RN. Laravel 13, PHP 8.3+, Tailwind CSS 4 (Vite).

## Desenvolvimento local

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
npm run build          # ou: npm run dev (Vite com recarga automática)
php artisan serve      # http://localhost:8000
php artisan test       # testes
vendor/bin/pint        # formatação do código
```

O `composer.json` fixa a plataforma em PHP 8.3 (`config.platform.php`): o Composer da Hostinger roda em 8.3, e sem essa trava o lock pode puxar pacotes que exigem 8.4.

## Onde mudar cada coisa

| O quê | Onde |
|---|---|
| Textos, serviços, portfólio, processo, dúvidas, depoimentos | `config/site.php` |
| URLs antigas (301) | `config/redirects.php` |
| Cores, fontes e componentes visuais | `resources/css/app.css` (`@theme` e `@utility`) |
| Layout, cabeçalho, rodapé, SEO | `resources/views/components/` |
| Páginas | `resources/views/pages/` |
| Banner de cookies e eventos do GA4 | `resources/js/analytics.js` |

Regra de conteúdo: só publique números, depoimentos e casos que possam ser comprovados. Com a lista `testimonials` vazia, a seção de depoimentos não aparece.

## Variáveis de ambiente do site

| Variável | Uso |
|---|---|
| `SITE_GA4_ID` | Google Analytics 4. Vazio = sem analytics e sem banner. Carrega só após o "Aceitar". |
| `SITE_WHATSAPP_NUMBER` | Número dos botões de WhatsApp (só dígitos, com 55). |
| `SITE_CNPJ` | Exibido no rodapé quando preenchido. |
| `ACCESS_GUARD_USER` / `ACCESS_GUARD_PASS` | Senha no site inteiro (ambiente de dev). **Vazio em produção.** |

Fora de `APP_ENV=production`, toda resposta leva `X-Robots-Tag: noindex`.

## Segurança

- CSP sem `unsafe-inline`: só o próprio site e o Google Tag Manager/Analytics.
- Fontes servidas pelo próprio site (sem Google Fonts).
- Cabeçalhos: `nosniff`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, HSTS em HTTPS.

## Migração do Slim para o Laravel

A versão anterior (Slim 4 + Twig) continua nas branches `natalcode` e `main` e segue em produção até a troca.

| Fase | Conteúdo | Situação |
|---|---|---|
| 0 | Esqueleto Laravel 13, Tailwind 4, fontes locais, cabeçalhos de segurança, proteção do dev | concluída |
| 1 | Redesenho: início, serviços (5), portfólio, sobre, dúvidas, contato, 404, SEO, sitemap, redirecionamentos 301 | concluída |
| 2 | Formulário de contato (lead no banco + aviso por e-mail, reCAPTCHA, limite de envios), Política de Privacidade e Termos de Uso, GA4 `generate_lead` | a fazer |
| 3 | Publicação no dev (subdomínio) e revisão do conteúdo | a fazer |
| 4 | Deploy na Hostinger (GitHub Action + SSH, como no projeto Emotion) e troca da branch publicada | a fazer |
| 5 | Painel (Filament): leads, portfólio, depoimentos e dúvidas | a fazer |
