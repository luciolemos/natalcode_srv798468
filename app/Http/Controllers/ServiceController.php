<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ServiceController extends Controller
{
    public function show(string $slug): View
    {
        $service = config("site.services.{$slug}");

        abort_unless(is_array($service), 404);

        return view('pages.services.show', ['slug' => $slug, 'service' => $service]);
    }
}
