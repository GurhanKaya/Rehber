<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 min-h-screen flex flex-col">
    <nav class="bg-gray-900 shadow-md p-4 text-white" role="navigation" aria-label="Ana navigasyon">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ route('admin.home') }}" class="font-bold text-xl hover:text-gray-300 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-900 rounded" aria-label="Admin Panel ana sayfa">Admin Panel</a>
            
            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center space-x-2 xl:space-x-4">
                <a href="{{ route('admin.users') }}" class="flex items-center font-semibold px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm xl:text-base">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Kullanıcılar</span>
                </a>
                <a href="{{ route('admin.tasks') }}" class="flex items-center font-semibold px-4 py-2 rounded-md bg-orange-600 hover:bg-orange-700 transition focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm xl:text-base">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Görevler</span>
                </a>
                <a href="{{ route('admin.appointments') }}" class="flex items-center font-semibold px-4 py-2 rounded-md bg-green-600 hover:bg-green-700 transition focus:outline-none focus:ring-2 focus:ring-green-500 text-sm xl:text-base">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Randevular</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="flex items-center bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition focus:outline-none focus:ring-2 focus:ring-red-500 text-sm xl:text-base">
                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Çıkış</span>
                    </button>
                </form>
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
            <a href="{{ route('admin.users') }}" class="flex items-center font-semibold px-4 py-3 rounded-md bg-indigo-600 hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-indigo-500" role="menuitem">
                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Kullanıcılar</span>
            </a>
            <a href="{{ route('admin.tasks') }}" class="flex items-center font-semibold px-4 py-3 rounded-md bg-orange-600 hover:bg-orange-700 transition focus:outline-none focus:ring-2 focus:ring-orange-500" role="menuitem">
                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Görevler</span>
            </a>
            <a href="{{ route('admin.appointments') }}" class="flex items-center font-semibold px-4 py-3 rounded-md bg-green-600 hover:bg-green-700 transition focus:outline-none focus:ring-2 focus:ring-green-500" role="menuitem">
                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Randevular</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="w-full text-left flex items-center bg-red-600 text-white px-4 py-3 rounded-md hover:bg-red-700 transition focus:outline-none focus:ring-2 focus:ring-red-500" role="menuitem">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Çıkış</span>
                </button>
            </form>
        </div>
    </nav>

    <main class="flex-1 container mx-auto px-4 sm:px-6 py-4 sm:py-6" role="main">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 dark:bg-green-900/50 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg" role="alert">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 dark:bg-red-900/50 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg" role="alert">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        {{ $slot ?? '' }}
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
