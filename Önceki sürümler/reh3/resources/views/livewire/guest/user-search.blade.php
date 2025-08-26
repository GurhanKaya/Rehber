@php use Illuminate\Support\Facades\Storage; @endphp
<div class="w-full max-w-7xl mx-auto" x-data="{ 
    open: @entangle('showFilters'), 
    view: @entangle('viewMode'),
    preventImageDownload(e) { //Fotoğraf seçmeyi engellemek için
        e.preventDefault();
        return false;
    }
}">
    <!-- Fotoğraf seçmeyi engellemek için -->
    <style>
        .user-photo {
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -o-user-select: none;
            user-select: none;
            -webkit-user-drag: none;
            -khtml-user-drag: none;
            -moz-user-drag: none;
            -o-user-drag: none;
            user-drag: none;
            pointer-events: none;
        }
    </style>

    <!-- Hoş Geldiniz Mesajı -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-700 dark:to-purple-700 text-white rounded-lg shadow-lg p-6 mb-6">
        <div class="text-center">
            <div class="flex justify-center mb-4">
                <div class="bg-white/20 backdrop-blur-sm rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
            <h1 class="text-2xl font-bold mb-2">Rehber Uygulamasına Hoş Geldiniz</h1>
            <p class="text-blue-100">Kişileri aratabilir ve kolayca randevu alabilirsiniz</p>
        </div>
    </div>

    <!-- Filtreler Alanı -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6 mb-6 space-y-6">
        <form wire:submit.prevent="search" class="space-y-4">
            <!-- Arama Alanı ve Butonlar -->
            <div class="flex flex-col md:flex-row gap-4">
                <input
                    type="text"
                    wire:model.debounce.500ms="query"
                    placeholder="İsim, soyad, unvan, departman"
                    class="flex-1 px-4 py-3 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600 dark:text-white"
                />
                <button
                    type="submit"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                >
                    Ara
                </button>
            </div>

            <!-- Filtre Göster / Gizle ve Görünüm Seçimi -->
            <div class="flex justify-between items-center">
                <button type="button" wire:click="toggleFilters" class="flex items-center space-x-2 text-white bg-blue-600 px-4 py-2 rounded-lg">
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    <span>Filtreler</span>
                </button>

                <div class="flex space-x-2">
                    <button type="button" x-on:click="view = 'grid'" class="p-2 rounded-lg" :class="view === 'grid' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-zinc-700'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h4v4H4V6zm6 0h4v4h-4V6zm6 0h4v4h-4V6M4 14h4v4H4v-4zm6 0h4v4h-4v-4zm6 0h4v4h-4v-4z" />
                        </svg>
                    </button>
                    <button type="button" x-on:click="view = 'list'" class="p-2 rounded-lg" :class="view === 'list' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-zinc-700'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Detaylı Filtre Alanları -->
            <div x-show="open" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <select wire:model="selectedTitle" class="px-4 py-3 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600 dark:text-white">
                    <option value="">Tüm Unvanlar</option>
                    @foreach($titles as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>

                <select wire:model="selectedDepartment" class="px-4 py-3 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600 dark:text-white">
                    <option value="">Tüm Departmanlar</option>
                    @foreach($departments as $d)
                        <option value="{{ $d }}">{{ $d }}</option>
                    @endforeach
                </select>

                <label class="flex items-center space-x-2 text-gray-800 dark:text-gray-100">
                    <input type="checkbox" wire:model="hasPhone" class="rounded text-blue-600">
                    <span>Telefonu olanlar</span>
                </label>

                <label class="flex items-center space-x-2 text-gray-800 dark:text-gray-100">
                    <input type="checkbox" wire:model="hasEmail" class="rounded text-blue-600">
                    <span>E-postası olanlar</span>
                </label>
            </div>
        </form>
    </div>

    <!-- Sonuçlar Alanı -->
    @if($searched && $users->count())
        <div :class="view === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6' : 'flex flex-col gap-4'">
            @foreach($users as $user)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5" :class="view === 'list' ? 'flex items-center space-x-4' : ''">
                    <div class="shrink-0" :class="view === 'list' ? 'w-16' : 'w-24 mx-auto mb-4'">
                        @if($user->photo && Storage::disk('public')->exists($user->photo))
                            <img class="rounded-full object-cover user-photo" 
                                :class="view === 'list' ? 'w-16 h-16' : 'w-24 h-24'" 
                                src="{{ url('storage/' . $user->photo) }}" 
                                alt="{{ $user->name }} {{ $user->surname }}"
                                @contextmenu.prevent
                                draggable="false">
                        @else
                            <div class="rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center"
                                 :class="view === 'list' ? 'w-16 h-16' : 'w-24 h-24'">
                                <span class="text-2xl font-bold text-gray-600 dark:text-gray-300">{{ $user->initials() }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1" :class="view === 'grid' ? 'text-center mt-4' : ''">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                            {{ $user->name }} {{ $user->surname }}
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-1">{{ $user->title }} - {{ $user->department }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-200">📞 {{ $user->phone ?? '—' }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-200">✉️ {{ $user->email ?? '—' }}</p>
                        <div class="mt-4 flex" :class="view === 'grid' ? 'justify-center' : 'justify-end'">
                            <a href="/randevu/{{ $user->id }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Randevu Al</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    @elseif($searched)
        <div class="text-center text-gray-500 dark:text-gray-400 py-10">
            Sonuç bulunamadı.
        </div>
    @endif
</div>
