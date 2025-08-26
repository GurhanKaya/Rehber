<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ __('app.personel_panel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 min-h-screen flex flex-col">
    <nav class="bg-gray-900 shadow-md p-4 text-white" role="navigation" aria-label="{{ __('app.main_navigation') }}">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ route('personel.home') }}" class="font-bold text-xl hover:text-gray-300 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-900 rounded" aria-label="{{ __('app.personel_panel') }}">{{ __('app.personel_panel') }}</a>
            
            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center space-x-2 xl:space-x-4">
                <a href="{{ route('personel.users') }}" class="flex items-center font-semibold px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm xl:text-base">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>{{ __('app.menu_users') }}</span>
                </a>
                <a href="{{ route('personel.tasks') }}" class="flex items-center font-semibold px-4 py-2 rounded-md bg-orange-600 hover:bg-orange-700 transition focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm xl:text-base">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ __('app.menu_tasks') }}</span>
                </a>
                <a href="{{ route('personel.randevularim') }}" class="flex items-center font-semibold px-4 py-2 rounded-md bg-green-600 hover:bg-green-700 transition focus:outline-none focus:ring-2 focus:ring-green-500 text-sm xl:text-base">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ __('app.menu_appointments') }}</span>
                </a>
                <a href="{{ route('personel.profile.edit') }}" class="flex items-center font-semibold px-4 py-2 rounded-md bg-teal-600 hover:bg-teal-700 transition focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm xl:text-base">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>{{ __('app.menu_profile') }}</span>
                </a>
                <x-logout-button />
            </div>

            <!-- Mobile Menu Button -->
            <button 
                class="lg:hidden text-white p-2 rounded-md hover:bg-gray-800 transition focus:outline-none focus:ring-2 focus:ring-blue-500" 
                onclick="toggleMobileMenu()"
                aria-expanded="false"
                aria-controls="mobileMenu"
                aria-label="Menü"
                id="mobileMenuButton">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden lg:hidden mt-4 space-y-2 border-t border-gray-700 pt-4" role="menu">
            <a href="{{ route('personel.users') }}" class="flex items-center font-semibold px-4 py-3 rounded-md bg-indigo-600 hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-indigo-500" role="menuitem">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>{{ __('app.menu_users') }}</span>
            </a>
            <a href="{{ route('personel.tasks') }}" class="flex items-center font-semibold px-4 py-3 rounded-md bg-orange-600 hover:bg-orange-700 transition focus:outline-none focus:ring-2 focus:ring-orange-500" role="menuitem">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ __('app.menu_tasks') }}</span>
            </a>
            <a href="{{ route('personel.randevularim') }}" class="flex items-center font-semibold px-4 py-3 rounded-md bg-green-600 hover:bg-green-700 transition focus:outline-none focus:ring-2 focus:ring-green-500" role="menuitem">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>{{ __('app.menu_appointments') }}</span>
            </a>
            <a href="{{ route('personel.profile.edit') }}" class="flex items-center px-4 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition focus:outline-none focus:ring-2 focus:ring-teal-500" role="menuitem">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>{{ __('app.menu_profile') }}</span>
            </a>
            <x-logout-button class="block w-full text-left flex items-center justify-center" />
        </div>
    </nav>

    <main class="flex-1 container mx-auto p-4 sm:p-6" role="main">
        {{ $slot }}
    </main>

    <script>
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            const button = document.getElementById('mobileMenuButton');
            const isHidden = mobileMenu.classList.contains('hidden');
            
            mobileMenu.classList.toggle('hidden');
            button.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
            
            // Keyboard navigation support
            if (!isHidden) {
                // Menu kapatılıyorsa focus'u button'a geri döndür
                button.focus();
            } else {
                // Menu açılıyorsa ilk link'e focus ver
                const firstLink = mobileMenu.querySelector('a');
                if (firstLink) firstLink.focus();
            }
        }

        // ESC tuşu ile menüyü kapatma
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const mobileMenu = document.getElementById('mobileMenu');
                const button = document.getElementById('mobileMenuButton');
                if (!mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                    button.setAttribute('aria-expanded', 'false');
                    button.focus();
                }
            }
        });

        // Dışarı tıklayınca menüyü kapatma
        document.addEventListener('click', function(e) {
            const mobileMenu = document.getElementById('mobileMenu');
            const button = document.getElementById('mobileMenuButton');
            const nav = e.target.closest('nav');
            
            if (!nav && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                button.setAttribute('aria-expanded', 'false');
            }
        });
    </script>
    @livewireScripts
</body>
</html>
