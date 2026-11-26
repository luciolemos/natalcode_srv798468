<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('services.index'), 'priority' => '0.9'],
        ];

        foreach (array_keys(config('site.services')) as $slug) {
            $urls[] = ['loc' => route('services.show', $slug), 'priority' => '0.8'];
        }

        foreach (['portfolio', 'about', 'faq', 'contact', 'privacy', 'terms'] as $name) {
            if (Route::has($name)) {
                $urls[] = ['loc' => route($name), 'priority' => in_array($name, ['privacy', 'terms'], true) ? '0.3' : '0.7'];
            }
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
