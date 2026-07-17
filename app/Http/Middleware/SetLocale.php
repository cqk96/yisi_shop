<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    private array $locales = ['zh_CN', 'en', 'es'];

    public function handle(Request $request, Closure $next)
    {
        $locale = $request->session()->get('locale', config('app.locale'));

        if (! in_array($locale, $this->locales, true)) {
            $locale = 'zh_CN';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
