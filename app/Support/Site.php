<?php

namespace App\Support;

/**
 * Atalhos para dados do site (config/site.php) usados nas views.
 */
class Site
{
    public static function whatsappUrl(?string $message = null): string
    {
        $number = preg_replace('/\D+/', '', (string) config('site.contact.whatsapp'));
        $text = $message ?? (string) config('site.contact.whatsapp_message');

        return 'https://wa.me/'.$number.'?text='.rawurlencode($text);
    }

    /**
     * @return array<string, mixed>
     */
    public static function organizationSchema(): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            'name' => config('site.name'),
            'legalName' => config('site.legal_name'),
            'description' => config('site.description'),
            'url' => url('/'),
            'logo' => asset('images/brand/nc-icon.png'),
            'email' => config('site.contact.email'),
            'areaServed' => 'BR',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Natal',
                'addressRegion' => 'RN',
                'addressCountry' => 'BR',
            ],
            'sameAs' => array_values(array_column(config('site.social', []), 'url')),
        ]);
    }
}
