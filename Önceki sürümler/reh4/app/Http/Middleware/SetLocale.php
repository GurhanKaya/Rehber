<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

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
        // Kullanıcı dilini önceliklendir, yoksa session, yoksa config
        $locale = null;

        if (Auth::check() && !empty(Auth::user()->locale)) {
            $locale = Auth::user()->locale;
        }

        if (!$locale) {
            $locale = Session::get('locale', config('app.locale'));
        }
        
        // Desteklenen diller
        $supportedLocales = ['tr', 'en'];
        
        // Eğer locale desteklenmiyorsa varsayılan olarak config'den al
        if (!in_array($locale, $supportedLocales)) {
            $locale = config('app.locale');
        }
        
        // Locale'i hem App hem de Session'a set et
        App::setLocale($locale);
        Session::put('locale', $locale);
        
        return $next($request);
    }
}
