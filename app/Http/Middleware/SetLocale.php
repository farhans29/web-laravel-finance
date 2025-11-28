<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale');
        if ($locale && in_array($locale, ['en', 'id'])) {
            App::setLocale($locale);
        } elseif (auth()->check() && property_exists(auth()->user(), 'locale') && auth()->user()->locale) {
            App::setLocale(auth()->user()->locale);
        }

        return $next($request);
    }
}