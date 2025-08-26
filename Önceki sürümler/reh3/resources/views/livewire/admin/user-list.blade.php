@php use Illuminate\Support\Facades\Storage; @endphp
<div class="w-full max-w-7xl mx-auto" x-data="{ 
    open: @entangle('showFilters'), 
    view: @entangle('viewMode'),
    preventImageDownload(e) {
        e.preventDefault();
        return false;
    }
}" role="main" aria-label="Kullanıcı listesi">

    <style>
        .user-photo {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            pointer-events: none;
        }
    </style>

    <!-- Header Section with "Yeni Kişi Ekle" Button -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Kullanıcı Yönetimi</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Sistemdeki tüm kullanıcıları görüntüleyin ve yönetin</p>
            </div>
            <a href="{{ route('admin.users.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Yeni Kişi Ekle
            </a>
        </div>
    </div>

    <!-- Filtreler Alanı -->
    <section class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 sm:p-6 mb-6 space-y-6" role="search" aria-label="Kullanıcı arama ve filtreleme">
        <form wire:submit.prevent="search" class="space-y-4">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <label for="search-input" class="sr-only">Kullanıcı ara</label>
                    <input
                        id="search-input"
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
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition"
                    aria-label="Arama yap"
                >
                    <span class="hidden sm:inline">Ara</span>
                    <svg class="w-5 h-5 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
                @if($searched)
                    <button
                        type="button"
                        wire:click="clearFilters"
                        class="px-4 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition"
                        aria-label="Filtreleri temizle"
                    >
                        <span class="hidden sm:inline">Temizle</span>
                        <svg class="w-5 h-5 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif
            </div>

            <!-- Filter Toggle and View Selection -->
            <div class="flex flex-wrap gap-2 items-center justify-between">
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

                <!-- View Mode Toggle -->
                <div class="flex bg-gray-100 dark:bg-zinc-700 rounded-lg p-1" role="group" aria-label="Görünüm modu seçimi">
                    <button
                        type="button"
                        wire:click="$set('viewMode', 'grid')"
                        class="px-3 py-2 rounded-md text-sm font-medium transition"
                        :class="view === 'grid' ? 'bg-white dark:bg-zinc-600 text-gray-900 dark:text-gray-100 shadow' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                        aria-label="Kart görünümü"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                    </button>
                    <button
                        type="button"
                        wire:click="$set('viewMode', 'list')"
                        class="px-3 py-2 rounded-md text-sm font-medium transition"
                        :class="view === 'list' ? 'bg-white dark:bg-zinc-600 text-gray-900 dark:text-gray-100 shadow' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                        aria-label="Liste görünümü"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Detaylı Filtre Alanları -->
            <div 
                x-show="open" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 bg-gray-50 dark:bg-zinc-900 p-4 rounded-lg border border-gray-200 dark:border-zinc-600"
                id="filter-panel"
                role="group"
                aria-label="Gelişmiş filtre seçenekleri"
            >
                <div>
                    <label for="department-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Departman</label>
                    <select 
                        id="department-filter"
                        wire:model="selectedDepartment" 
                        class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        aria-label="Departman filtresi"
                    >
                        <option value="">Tümü</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="title-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unvan</label>
                    <select 
                        id="title-filter"
                        wire:model="selectedTitle" 
                        class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        aria-label="Unvan filtresi"
                    >
                        <option value="">Tümü</option>
                        @foreach($titles as $title)
                            <option value="{{ $title }}">{{ $title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">İletişim</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                wire:model="hasPhone" 
                                class="rounded border-gray-300 text-blue-600 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            />
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Telefonu var</span>
                        </label>
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                wire:model="hasEmail" 
                                class="rounded border-gray-300 text-blue-600 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            />
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">E-postası var</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-end">
                    <button
                        type="button"
                        wire:click="clearFilters"
                        class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition"
                    >
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Sıfırla
                    </button>
                </div>
            </div>
        </form>
    </section>

    <!-- Sonuçlar Alanı -->
    @if($searched && $users->count())
        <section aria-label="Kullanıcı listesi sonuçları" role="region">
            <div class="sr-only" aria-live="polite">{{ $users->total() }} kullanıcı bulundu</div>
            <div :class="view === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 lg:gap-6' : 'flex flex-col gap-4'">
                @foreach($users as $user)
                    <article class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 sm:p-5 hover:shadow-lg transition-shadow" :class="view === 'list' ? 'flex items-center space-x-4' : ''">
                        <div class="shrink-0" :class="view === 'list' ? 'w-16' : 'w-20 sm:w-24 mx-auto mb-4'">
                            @if($user->photo && Storage::disk('public')->exists($user->photo))
                                <img class="rounded-full object-cover user-photo" 
                                    :class="view === 'list' ? 'w-16 h-16' : 'w-20 h-20 sm:w-24 sm:h-24'" 
                                    src="{{ url('storage/' . $user->photo) }}" 
                                    alt="{{ $user->name }} {{ $user->surname }} profil fotoğrafı"
                                    @contextmenu.prevent
                                    draggable="false">
                            @else
                                <div class="rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center"
                                     :class="view === 'list' ? 'w-16 h-16' : 'w-20 h-20 sm:w-24 sm:h-24'"
                                     role="img"
                                     aria-label="{{ $user->name }} {{ $user->surname }} profil fotoğrafı bulunmuyor">
                                    <span class="text-lg sm:text-2xl font-bold text-gray-600 dark:text-gray-300">{{ $user->initials() }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1" :class="view === 'grid' ? 'text-center mt-4' : ''">
                            <h3 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-100">
                                {{ $user->name }} {{ $user->surname }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-1">{{ $user->title }} - {{ $user->department }}</p>
                            <div class="text-sm text-gray-700 dark:text-gray-200 space-y-1">
                                <p><span class="sr-only">Telefon: </span>📞 {{ $user->phone ?? '—' }}</p>
                                <p><span class="sr-only">E-posta: </span>✉️ {{ $user->email ?? '—' }}</p>
                            </div>
                            <div class="mt-3 sm:mt-4 flex flex-wrap gap-2" :class="view === 'grid' ? 'justify-center' : 'justify-end'">
                                @if($user->role === 'personel')
                                    <a 
                                        href="{{ route('guest.book-appointment', $user->id) }}"
                                        class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition text-sm"
                                        aria-label="{{ $user->name }} {{ $user->surname }} için randevu al"
                                        target="_blank"
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Randevu Al
                                    </a>
                                @endif
                                <a 
                                    href="{{ route('admin.users.edit', $user->id) }}" 
                                    class="inline-flex items-center px-3 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition text-sm"
                                    aria-label="{{ $user->name }} {{ $user->surname }} kullanıcısını düzenle"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Düzenle
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="mt-6" role="navigation" aria-label="Sayfalama">
                {{ $users->links() }}
            </div>
        </section>
    @elseif($searched)
        <div class="text-center text-gray-500 dark:text-gray-400 py-10" role="status">
            <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Sonuç bulunamadı</h3>
            <p class="text-gray-500 dark:text-gray-400">Arama kriterlerinizi değiştirip tekrar deneyin.</p>
        </div>
    @else
        <div class="text-center text-gray-500 dark:text-gray-400 py-10" role="status">
            <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Kullanıcı arayın</h3>
            <p class="text-gray-500 dark:text-gray-400">Kullanıcıları listelemek için arama yapın.</p>
        </div>
    @endif
</div>
