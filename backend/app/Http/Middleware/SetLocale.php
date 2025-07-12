<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['en', 'pt'];
        $locale = $this->getLocaleFromRequest($request);

        if (!in_array($locale, $supportedLocales)) {
            $locale = config('app.locale', 'en');
        }

        App::setLocale($locale);
        return $next($request);
    }

    private function getLocaleFromRequest(Request $request): string
    {
        if ($request->has('lang')) {
            return $request->input('lang');
        }

        if ($request->hasHeader('X-Language')) {
            return $request->header('X-Language');
        }

        $acceptLanguage = $request->header('Accept-Language');
        if ($acceptLanguage) {
            $locale = substr($acceptLanguage, 0, 2);
            return strtolower($locale);
        }

        return config('app.locale', 'en');
    }
}
