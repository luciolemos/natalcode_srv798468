<?php

use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');

Route::view('/servicos', 'pages.services.index')->name('services.index');
Route::get('/servicos/{slug}', [ServiceController::class, 'show'])->name('services.show');

Route::view('/portfolio', 'pages.portfolio')->name('portfolio');
Route::view('/sobre', 'pages.about')->name('about');
Route::view('/faq', 'pages.faq')->name('faq');
Route::view('/contato', 'pages.contact')->name('contact');
Route::view('/politica-de-privacidade', 'pages.privacy')->name('privacy');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

// URLs do site antigo (Slim): redirecionamento permanente.
foreach (config('redirects') as $from => $to) {
    Route::permanentRedirect($from, $to);
}

// Eventos da agenda do CEDE (/agenda/{slug} e /agenda/{slug}/ics).
Route::permanentRedirect('/agenda/{any}', '/')->where('any', '.*');
