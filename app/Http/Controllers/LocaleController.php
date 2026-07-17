<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    private array $locales = ['zh_CN', 'en', 'es'];

    public function switch(string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, $this->locales, true), 404);

        session(['locale' => $locale]);

        return back();
    }
}
