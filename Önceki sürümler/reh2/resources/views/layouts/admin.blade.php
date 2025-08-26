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
    <nav class="bg-gray-900 shadow-md p-4 text-white">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ route('admin.home') }}" class="font-bold text-xl hover:text-gray-300 transition">Admin Panel</a>
            
            <!-- Desktop Menu -->
            <div class="hidden md:flex space-x-4">
                <a href="{{ route('admin.users') }}" class="font-semibold px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 transition">Kullanıcılar</a>
                <a href="{{ route('admin.users.create') }}" class="inline-block px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">Yeni Kullanıcı</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition">Çıkış</button>
                </form>
            </div>

            <!-- Mobile Menu Button -->
            <button class="md:hidden text-white" onclick="toggleMobileMenu()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden mt-4 space-y-2">
            <a href="{{ route('admin.users') }}" class="block font-semibold px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 transition">Kullanıcılar</a>
            <a href="{{ route('admin.users.create') }}" class="block px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">Yeni Kullanıcı</a>
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="w-full text-left bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition">Çıkış</button>
            </form>
        </div>
    </nav>

    <main class="flex-1 container mx-auto p-6">
        {{ $slot }}
    </main>

    <script>
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        }
    </script>

    @livewireScripts
</body>
</html>
