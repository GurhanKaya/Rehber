<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
{
    /**
     * Dil değiştirme
     */
    public function changeLanguage($locale)
    {
        // Desteklenen diller
        $supportedLocales = ['tr', 'en'];
        
        if (in_array($locale, $supportedLocales)) {
            // Önce session'a set et
            Session::put('locale', $locale);
            
            // Sonra App locale'ini güncelle
            App::setLocale($locale);

            // Oturum açmış kullanıcının kalıcı dili güncellensin
            if (Auth::check()) {
                $user = Auth::user();
                $user->locale = $locale;
                $user->save();
            }
            
            // Flash message ekle
            $localeName = $locale === 'tr' ? 'Türkçe' : 'English';
            Session::flash('success', __('app.language_changed', ['language' => $localeName]));
        }
        
        return redirect()->back();
    }
}
