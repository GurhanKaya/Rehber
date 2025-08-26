@php use Illuminate\Support\Facades\Storage; @endphp
<div class="w-full max-w-6xl mx-auto mt-4 sm:mt-8" role="main" aria-label="{{ __('app.user_list') }}" x-data="{ 
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
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('app.users_heading') }}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('app.users_subheading') }}</p>
        </header>
        
        <x-search.toolbar 
            :titles="$titles" 
            :departments="$departments" 
            :show-clear="$searched || $selectedDepartment || $selectedTitle || $hasPhone || $hasEmail"
            :has-phone-value="$hasPhone"
            :has-email-value="$hasEmail"
        />

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
            <div :class="view === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6' : (view === 'wide' ? 'grid grid-cols-1 lg:grid-cols-2 gap-6' : 'flex flex-col gap-4')">
                @foreach($users as $user)
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 shadow-sm hover:shadow-md transition p-5" :class="view === 'list' ? 'flex items-center space-x-4' : ''">
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
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-1">{{ $user->title_name ?? '—' }} - {{ $user->department_name ?? '—' }}</p>
                            @if(!empty($user->phone))
                                <div class="mt-1 flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.95.684l1.2 3.6a1 1 0 01-.25 1.024l-1.5 1.5a16 16 0 006.364 6.364l1.5-1.5a1 1 0 011.024-.25l3.6 1.2a1 1 0 01.684.95V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <a href="tel:{{ $user->phone }}" class="hover:underline">{{ $user->phone }}</a>
                                </div>
                            @else
                                <div class="mt-1 text-sm text-gray-400 dark:text-gray-500">—</div>
                            @endif
                            @if(!empty($user->email))
                                <div class="mt-1 flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-18 8h18a2 2 0 002-2V8a2 2 0 00-2-2H3a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                    </svg>
                                    <a href="mailto:{{ $user->email }}" class="hover:underline">{{ $user->email }}</a>
                                </div>
                            @else
                                <div class="mt-1 text-sm text-gray-400 dark:text-gray-500">—</div>
                            @endif
                            <div class="mt-4 flex" :class="view === 'grid' ? 'justify-center' : 'justify-end'">
                                <a href="/randevu/{{ $user->id }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('app.book_appointment_for_user', ['name' => $user->name . ' ' . $user->surname]) }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">{{ __('app.book_appointment') }}</a>
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
        @else
            <div class="text-center py-12" role="status">
                <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('app.search_users_title') }}</h3>
                <p class="text-gray-500 dark:text-gray-400">{{ __('app.search_users_hint') }}</p>
            </div>
        @endif
    </div>
</div> 