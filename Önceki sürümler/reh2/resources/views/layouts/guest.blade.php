{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="tr" class="dark">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Rehber Arama</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav class="bg-gray-900 shadow-md">
        <div class="max-w-7xl mx-auto px-6 sm:px-12 lg:px-24">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-white select-none">Rehber</a>
                <div class="space-x-6">
                    <a href="{{ route('login') }}" class="text-white font-semibold px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 transition">Giriş</a>
                    <a href="{{ route('register') }}" class="text-white font-semibold px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 transition">Kayıt Ol</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Ana içerik -->
    <main class="flex-1 container mx-auto px-4 py-8">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="mt-auto bg-gray-900 border-t border-gray-800 py-6">
        <p class="text-center text-gray-400 text-sm select-none">© 2025 Rehber Projesi.</p>
    </footer>
</body>
</html>
