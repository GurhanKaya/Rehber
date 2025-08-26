@php use Illuminate\Support\Facades\Storage; @endphp
<div class="w-full max-w-6xl mx-auto mt-4 sm:mt-8" role="main" aria-label="Kullanıcı listesi" x-data="{ 
    open: @entangle('showFilters'), 
    view: @entangle('viewMode'),
    preventImageDownload(e) {
        e.preventDefault();
        return false;
    }
}">
    <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg shadow p-4 sm:p-6">
        <!-- Header -->
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Kullanıcılar</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Sistemdeki kullanıcıları görüntüleyin ve arayın</p>
        </header>
        
        <!-- Filtreler Alanı -->
        <section class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 sm:p-6 mb-6 space-y-6" role="search" aria-label="Kullanıcı arama ve filtreleme">
            <form wire:submit.prevent="search" class="space-y-4">
                <!-- Arama Alanı ve Butonlar -->
                <div class="flex flex-col lg:flex-row gap-4">
                    <div class="flex-1">
                        <label for="user-search" class="sr-only">Kullanıcı ara</label>
                        <input
                            id="user-search"
                            type="text"
                            wire:model.debounce.500ms="query"
                            placeholder="İsim, soyad, unvan, departman ara..."
                            class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            aria-describedby="search-help"
                        />
                        <div id="search-help" class="sr-only">İsim, soyad, unvan veya departman bilgisiyle arama yapabilirsiniz</div>
                    </div>
                    <button
                        type="submit"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition flex items-center justify-center"
                        aria-label="Arama yap"
                    >
                        <span class="mr-2">Ara</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    @if($searched || $selectedDepartment || $selectedTitle || $hasPhone || $hasEmail)
                        <button
                            type="button"
                            wire:click="clearSearch"
                            class="px-4 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition flex items-center justify-center"
                            aria-label="Filtreleri temizle"
                        >
                            <span class="mr-2">Temizle</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endif
                </div>

                <!-- Quick Filter Buttons and View Toggle -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex flex-wrap gap-2 items-center">
                        <button 
                            type="button" 
                            wire:click="toggleFilters" 
                            class="flex items-center space-x-2 text-white bg-blue-600 px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition"
                            aria-expanded="false"
                            :aria-expanded="open"
                            aria-controls="filter-panel"
                            aria-label="Gelişmiş filtreleri göster/gizle"
                        >
                            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            <span>Filtreler</span>
                        </button>
                    </div>

                    <div class="flex space-x-2" role="group" aria-label="Görünüm seçenekleri">
                        <button 
                            type="button" 
                            x-on:click="view = 'grid'" 
                            class="p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition" 
                            :class="view === 'grid' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-gray-300'"
                            :aria-pressed="view === 'grid'"
                            aria-label="Kart görünümü"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h4v4H4V6zm6 0h4v4h-4V6zm6 0h4v4h-4V6M4 14h4v4H4v-4zm6 0h4v4h-4v-4zm6 0h4v4h-4v-4z" />
                            </svg>
                        </button>
                        <button 
                            type="button" 
                            x-on:click="view = 'list'" 
                            class="p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition" 
                            :class="view === 'list' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-gray-300'"
                            :aria-pressed="view === 'list'"
                            aria-label="Liste görünümü"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Detaylı Filtre Alanları -->
                <div 
                    x-show="open" 
                    x-transition 
                    class="grid grid-cols-1 md:grid-cols-2 gap-4"
                    id="filter-panel"
                    role="group"
                    aria-label="Gelişmiş filtre seçenekleri"
                >
                    <div>
                        <label for="title-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unvan</label>
                        <select 
                            id="title-filter"
                            wire:model="selectedTitle" 
                            class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            aria-label="Unvan filtresi"
                        >
                            <option value="">Tüm Unvanlar</option>
                            @foreach($titles as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="department-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Departman</label>
                        <select 
                            id="department-filter"
                            wire:model="selectedDepartment" 
                            class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            aria-label="Departman filtresi"
                        >
                            <option value="">Tüm Departmanlar</option>
                            @foreach($departments as $d)
                                <option value="{{ $d }}">{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Telefon ve E-posta Filtreleri -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Özel Filtreler</label>
                        <div class="flex flex-wrap gap-2">
                            <button 
                                type="button" 
                                wire:click="filterByPhone" 
                                class="px-4 py-2 rounded-lg font-semibold transition @if($hasPhone) bg-green-600 text-white @else bg-gray-200 dark:bg-zinc-700 dark:text-white @endif"
                            >
                                📞 Telefonu Olanlar
                            </button>
                            
                            <button 
                                type="button" 
                                wire:click="filterByEmail" 
                                class="px-4 py-2 rounded-lg font-semibold transition @if($hasEmail) bg-purple-600 text-white @else bg-gray-200 dark:bg-zinc-700 dark:text-white @endif"
                            >
                                ✉️ E-postası Olanlar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </section>

        <!-- CSS for user photos -->
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
        @else
            <div class="text-center py-12" role="status">
                <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                    Kullanıcı arayın
                </h3>
                <p class="text-gray-500 dark:text-gray-400">
                    Kullanıcıları listelemek için arama yapın.
                </p>
            </div>
        @endif
    </div>
</div> 