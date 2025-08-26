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
        <div class="container mx-auto flex justify-between">
            <div class="font-bold text-xl">Admin Panel</div>
            <div class="space-x-4">
                <a href="{{ route('admin.users') }}" class="font-semibold px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 transition">Kullanıcılar</a>
                <a href="{{ route('admin.users.create') }}" class="font-semibold px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 transition">Yeni Kullanıcı</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition">Çıkış</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="flex-1 container mx-auto p-6">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
