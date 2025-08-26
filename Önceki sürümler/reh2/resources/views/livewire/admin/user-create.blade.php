<div class="p-6 max-w-3xl mx-auto bg-white dark:bg-zinc-800 rounded-xl shadow-md space-y-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Yeni Kullanıcı Ekle</h1>

    <form wire:submit.prevent="save" class="space-y-5">
        <!-- Fotoğraf Yükleme Alanı -->
        <div class="flex items-center space-x-6">
            <div class="shrink-0">
                @if ($photo)
                    <img class="h-24 w-24 object-cover rounded-full" src="{{ $photo->temporaryUrl() }}" alt="Profil fotoğrafı önizleme">
                @else
                    <div class="h-24 w-24 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center">
                        <span class="text-2xl font-bold text-gray-600 dark:text-gray-300">{{ strtoupper(substr($name,0,1)) }}{{ strtoupper(substr($surname,0,1)) }}</span>
                    </div>
                @endif
            </div>
            <div class="flex flex-col space-y-3 flex-1">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Profil Fotoğrafı
                    </label>
                    <input type="file" wire:model="photo" accept="image/*"
                           class="block w-full text-sm text-gray-500 dark:text-gray-300
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100
                                  dark:file:bg-zinc-700 dark:file:text-white">
                </div>
                @error('photo') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" wire:model.defer="name" placeholder="Ad"
                   class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white" />

            <input type="text" wire:model.defer="surname" placeholder="Soyad"
                   class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white" />

            <input type="email" wire:model.defer="email" placeholder="Email"
                   class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white" />

            <input type="password" wire:model.defer="password" placeholder="Şifre"
                   class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white" />

            <input type="text" wire:model.defer="title" placeholder="Ünvan"
                   class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white" />

            <input type="text" wire:model.defer="department" placeholder="Departman"
                   class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white" />

            <input type="text" wire:model.defer="phone" placeholder="Telefon: 5xx xxx xx xx"
                   class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white" />

            <select wire:model.defer="role"
                    class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white">
                <option value="">Rol Seçin</option>
                <option value="admin">Admin</option>
                <option value="personel">Personel</option>
            </select>
        </div>

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold p-3 rounded-lg transition duration-200">
            Kaydet
        </button>
    </form>
</div>
