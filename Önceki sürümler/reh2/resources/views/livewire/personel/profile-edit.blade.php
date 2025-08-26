<div class="p-6 max-w-3xl mx-auto bg-white dark:bg-zinc-800 rounded-xl shadow-md space-y-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Bilgilerimi Güncelle</h1>

    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 dark:bg-green-700 dark:text-white font-medium p-3 rounded-lg shadow">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="update" class="space-y-5">
        <!-- Fotoğraf Yükleme Alanı -->
        <div class="flex items-center space-x-6">
            <div class="shrink-0">
                @if ($photo)
                    <img class="h-24 w-24 object-cover rounded-full" src="{{ $photo->temporaryUrl() }}" alt="Profil fotoğrafı önizleme">
                @elseif($user->photo)
                    <img class="h-24 w-24 object-cover rounded-full" src="{{ Storage::url($user->photo) }}" alt="Mevcut profil fotoğrafı">
                @else
                    <div class="h-24 w-24 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center">
                        <span class="text-2xl font-bold text-gray-600 dark:text-gray-300">{{ $user->initials() }}</span>
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
                @if($user->photo)
                    <button type="button" wire:click="deletePhoto"
                            class="inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 w-full sm:w-auto">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Fotoğrafı Kaldır
                    </button>
                @endif
                @error('photo') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

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
        </div>

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold p-3 rounded-lg transition duration-200">
            Güncelle
        </button>
    </form>
</div> 