<div class="p-6 max-w-3xl mx-auto bg-white dark:bg-zinc-800 rounded-xl shadow-md space-y-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Kullanıcı Düzenle</h1>

    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 dark:bg-green-700 dark:text-white font-medium p-3 rounded-lg shadow">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="update" class="space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" wire:model.defer="name" placeholder="Ad"
                   class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white"/>

            <input type="text" wire:model.defer="surname" placeholder="Soyad"
                   class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white"/>

            <input type="email" wire:model.defer="email" placeholder="Email"
                   class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white"/>

            <input type="password" wire:model.defer="password" placeholder="Yeni Şifre (isteğe bağlı)"
                   class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white"/>

            <input type="text" wire:model.defer="title" placeholder="Ünvan"
                   class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white"/>

            <input type="text" wire:model.defer="department" placeholder="Departman"
                   class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white"/>

            <input type="text" wire:model.defer="phone" placeholder="Telefon"
                   class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white"/>

            <select wire:model.defer="role"
                    class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white">
                <option value="">Rol Seçin</option>
                <option value="admin">Admin</option>
                <option value="user">Kullanıcı</option>
            </select>
        </div>

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold p-3 rounded-lg transition duration-200">
            Güncelle
        </button>
        <button type="button" wire:click="delete"
            onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')"
            class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold p-3 rounded-lg transition duration-200">
            Sil
        </button>
    </form>
</div>
