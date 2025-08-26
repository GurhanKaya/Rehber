<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 Hata Test Sayfasý</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 min-h-screen flex flex-col">
    <nav class="bg-gray-900 shadow-md">
        <div class="max-w-7xl mx-auto px-6 sm:px-12 lg:px-24">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="text-2xl font-bold text-white select-none">Rehber</a>
                <div class="space-x-6">
                    <a href="/test-419" class="text-white font-semibold px-4 py-2 rounded-md bg-red-600 hover:bg-red-700 transition">419 Test</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1 container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-red-500 mb-8 text-center">419 CSRF Token Hatasý Test Sayfasý</h1>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Bu Form 419 Hatasý Verecek</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Aþaðýdaki form CSRF token olmadan gönderilecek ve 419 hatasý oluþturacak.</p>
                
                <form method="POST" action="/personel/users" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Arama Terimi:</label>
                        <input type="text" name="query" value="test arama" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Departman:</label>
                        <select name="department" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Tüm Departmanlar</option>
                            <option value="Bilgi Ýþlem">Bilgi Ýþlem</option>
                            <option value="Öðrenci Ýþleri">Öðrenci Ýþleri</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700 transition">
                        Bu Butona Týklayýn (419 Hatasý Oluþacak)
                    </button>
                </form>
            </div>
            
            <div class="bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 dark:border-yellow-600 rounded-lg p-4 mb-4">
                <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-2">Beklenen 419 Hatasý:</h3>
                <ul class="text-yellow-700 dark:text-yellow-300 space-y-1">
                    <li>Sayfa baþlýðý: "419 | Page Expired"</li>
                    <li>Ana mesaj: "CSRF token mismatch."</li>
                    <li>Detay: "The page has expired due to inactivity. Please refresh and try again."</li>
                </ul>
            </div>
            
            <div class="bg-blue-100 dark:bg-blue-900 border border-blue-400 dark:border-blue-600 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-2">Ekran Görüntüsü Ýçin Adýmlar:</h3>
                <ol class="text-blue-700 dark:text-blue-300 space-y-1">
                    <li>1. Bu sayfayý açýn</li>
                    <li>2. Developer Tools açýn (F12)</li>
                    <li>3. Network sekmesini açýn</li>
                    <li>4. "Bu Butona Týklayýn" butonuna basýn</li>
                    <li>5. 419 hata sayfasýnýn ekran görüntüsünü alýn</li>
                    <li>6. Network sekmesinde 419 status code unu gösterin</li>
                </ol>
            </div>
        </div>
    </main>

    <footer class="mt-auto bg-gray-900 border-t border-gray-800 py-6">
        <p class="text-center text-gray-400 text-sm select-none"> 2025 Rehber Projesi - 419 Test Sayfasý</p>
    </footer>
</body>
</html>
