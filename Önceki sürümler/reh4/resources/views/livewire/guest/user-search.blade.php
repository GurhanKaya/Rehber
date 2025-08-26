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
            <h1 class="text-2xl font-bold mb-2">{{ __('app.guest_welcome_title') }}</h1>
            <p class="text-blue-100">{{ __('app.guest_welcome_subtitle') }}</p>
        </div>
    </div>

    <x-search.toolbar 
        :titles="$titles" 
        :departments="$departments" 
        :show-clear="$searched || $selectedDepartment || $selectedTitle || $hasPhone || $hasEmail"
        query-model="query"
        search-action="search"
        toggle-filters-action="toggleFilters"
        clear-action="clearSearch"
        selected-title-model="selectedTitle"
        selected-department-model="selectedDepartment"
        has-phone-model="hasPhone"
        has-email-model="hasEmail"
    />

    <!-- Sonuçlar Alanı -->
    @if($searched && $users->count())
        <div :class="view === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6' : (view === 'wide' ? 'grid grid-cols-1 lg:grid-cols-2 gap-6' : 'grid grid-cols-1 md:grid-cols-2 gap-6')">
            @foreach($users as $user)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5" :class="view === 'list' ? 'flex items-center space-x-4' : ''">
                    <div class="shrink-0" :class="view === 'list' ? 'w-16' : 'w-24 mx-auto mb-4'">
                        @php $photoPath = $user->photo ? str_replace('\\', '/', $user->photo) : null; @endphp
                        @if($photoPath)
                            <img class="rounded-full object-cover user-photo" 
                                :class="view === 'list' ? 'w-16 h-16' : 'w-24 h-24'" 
                                src="{{ Storage::url($photoPath) }}" 
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
                            <a href="/randevu/{{ $user->id }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">{{ __('app.book_appointment') }}</a>
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
            {{ __('app.no_results') }}
        </div>
    @endif
</div>
