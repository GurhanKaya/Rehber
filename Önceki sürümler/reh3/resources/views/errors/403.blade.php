<!DOCTYPE html>
<html lang="tr" class="dark">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>403 - Erişim Engellendi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 min-h-screen flex items-center justify-center">
    <div class="max-w-md mx-auto text-center p-8">
        <!-- Error Icon -->
        <div class="mb-8">
            <div class="mx-auto w-24 h-24 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
        </div>

        <!-- Error Message -->
        <h1 class="text-4xl font-bold text-white mb-4">403</h1>
        <h2 class="text-xl font-semibold text-gray-300 mb-4">Erişim Engellendi</h2>
        <p class="text-gray-400 mb-8">
            Bu sayfaya doğrudan erişim engellenmiştir. Dosyaları güvenli bir şekilde indirmek için lütfen kendi panelinizi kullanın.
        </p>

        <!-- Action Buttons -->
        <div class="space-y-4">
            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.home') }}" 
                       class="inline-flex items-center justify-center w-full px-6 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Admin Paneline Dön
                    </a>
                @else
                    <a href="{{ route('personel.home') }}" 
                       class="inline-flex items-center justify-center w-full px-6 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Personel Paneline Dön
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" 
                   class="inline-flex items-center justify-center w-full px-6 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Giriş Yap
                </a>
            @endauth

            <!-- Ana Sayfa Linki -->
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center justify-center w-full px-6 py-4 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Güvenli Çıkış
                </button>
            </form>
        </div>

        <!-- Additional Info -->
        <div class="mt-8 p-4 bg-gray-800 rounded-lg">
            <p class="text-sm text-gray-400">
                <strong>Bilgi:</strong> Dosyaları güvenli bir şekilde indirmek için lütfen görev detay sayfalarındaki mavi indirme butonlarını kullanın.
            </p>
        </div>
    </div>
</body>
</html> 