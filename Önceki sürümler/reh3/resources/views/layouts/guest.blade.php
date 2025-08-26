{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="dark">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content="{{ __('app.meta_description') }}"/>
    <meta name="keywords" content="{{ __('app.meta_keywords') }}"/>
    <title>{{ $title ?? __('app.guide') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 min-h-screen flex flex-col">
    <!-- Skip Link for Screen Readers -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-blue-600 text-white px-4 py-2 rounded-lg z-50">{{ __('app.skip_to_main') }}</a>
    
    <!-- Navbar -->
    <nav class="bg-gray-900 shadow-md" role="navigation" aria-label="{{ __('app.main_navigation') }}">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('home') }}" class="text-xl sm:text-2xl font-bold text-white select-none hover:text-gray-300 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-900 rounded" aria-label="{{ __('app.home_page') }}">
                    {{ __('app.guide') }}
                </a>
                <div class="flex items-center space-x-3 sm:space-x-6">
                    <!-- Dil Değiştirme Butonları -->
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('language.change', 'tr') }}" 
                           class="px-2 py-1 text-sm rounded {{ app()->getLocale() === 'tr' ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }} transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                           title="{{ __('app.turkish') }}">
                            TR
                        </a>
                        <a href="{{ route('language.change', 'en') }}" 
                           class="px-2 py-1 text-sm rounded {{ app()->getLocale() === 'en' ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }} transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                           title="{{ __('app.english') }}">
                           EN
                        </a>
                    </div>
                    
                    <a href="{{ route('login') }}" class="text-white font-semibold px-3 sm:px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-900 transition text-sm sm:text-base">
                        {{ __('auth.log_in') }}
                    </a>
                    <a href="{{ route('register') }}" class="text-white font-semibold px-3 sm:px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-900 transition text-sm sm:text-base">
                        {{ __('auth.sign_up') }}
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb (if needed) -->
    @if(isset($breadcrumbs))
        <nav aria-label="{{ __('app.page_location') }}" class="bg-gray-800 border-b border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <ol class="flex items-center space-x-2 py-3 text-sm">
                    @foreach($breadcrumbs as $breadcrumb)
                        <li class="flex items-center">
                            @if(!$loop->last)
                                <a href="{{ $breadcrumb['url'] }}" class="text-gray-300 hover:text-white transition">{{ $breadcrumb['title'] }}</a>
                                <svg class="w-4 h-4 mx-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            @else
                                <span class="text-white font-medium" aria-current="page">{{ $breadcrumb['title'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </nav>
    @endif

    <!-- Ana içerik -->
    <main id="main-content" class="flex-1 container mx-auto px-4 py-6 sm:py-8" role="main">
        <!-- Error/Success Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="mt-auto bg-gray-900 border-t border-gray-800 py-6" role="contentinfo">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-center">
                <p class="text-center text-gray-400 text-sm select-none">
                    © 2025 {{ __('app.guide_project') }}. {{ __('app.all_rights_reserved') }}.
                </p>
                <div class="flex space-x-4 mt-2 sm:mt-0">
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition focus:outline-none focus:ring-2 focus:ring-blue-500 rounded">{{ __('app.privacy') }}</a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition focus:outline-none focus:ring-2 focus:ring-blue-500 rounded">{{ __('app.terms') }}</a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition focus:outline-none focus:ring-2 focus:ring-blue-500 rounded">{{ __('app.support') }}</a>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
