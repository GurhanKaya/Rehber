@php use Illuminate\Support\Facades\Storage; @endphp
<div class="w-full max-w-7xl mx-auto" x-data="{ 
    open: @entangle('showFilters'), 
    view: @entangle('viewMode'),
    preventImageDownload(e) {
        e.preventDefault();
        return false;
    }
}" role="main" aria-label="{{ __('app.user_list_aria') }}">

    <style>
        .user-photo { user-select: none; pointer-events: none; }
    </style>

    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ __('app.user_management') }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('app.user_management_sub') }}</p>
            </div>
            <a href="{{ route('admin.users.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('app.add_new_user') }}
            </a>
        </div>
    </div>

    <section class="mb-6" role="search" aria-label="{{ __('app.user_search_filter_aria') }}">
        <x-search.toolbar 
            :show-clear="$searched || $selectedDepartment || $selectedTitle || $hasPhone || $hasEmail"
            query-model="query"
            search-action="search"
            toggle-filters-action="toggleFilters"
            clear-action="clearSearch"
            :show-advanced="false"
            :show-view-options="true"
            :show-filter-toggle="true"
        />

        <div 
            x-show="open" 
            x-transition
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 bg-gray-50 dark:bg-zinc-900 p-4 rounded-lg"
            id="filter-panel"
            role="group"
            aria-label="{{ __('app.advanced_filter_options_aria') }}"
        >
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.department') }}</label>
                <select 
                    wire:model="selectedDepartment" 
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">{{ __('app.all') }}</option>
                    @foreach($departments as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.title') }}</label>
                <select 
                    wire:model="selectedTitle" 
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">{{ __('app.all') }}</option>
                    @foreach($titles as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.contact') }}</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            wire:model="hasPhone" 
                            class="rounded border-gray-300 text-blue-600 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        />
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('app.has_phone') }}</span>
                    </label>
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            wire:model="hasEmail" 
                            class="rounded border-gray-300 text-blue-600 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        />
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('app.has_email') }}</span>
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
                    {{ __('app.reset') }}
                </button>
            </div>
        </div>
    </section>

    @if($searched && $users->count())
        <section aria-label="{{ __('app.user_list_results_aria') }}" role="region">
            <div class="sr-only" aria-live="polite">{{ __('app.users_found', ['count' => $users->total()]) }}</div>
            <div :class="view === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 lg:gap-6' : 'grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6'">
                @foreach($users as $user)
                    <article class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 sm:p-5 hover:shadow-lg transition-shadow" :class="view === 'list' ? 'flex items-center space-x-4' : ''">
                        <div class="shrink-0" :class="view === 'list' ? 'w-16' : 'w-20 sm:w-24 mx-auto mb-4'">
                            @php $photoPath = $user->photo ? str_replace('\\', '/', $user->photo) : null; @endphp
                            @if($photoPath)
                                <img class="rounded-full object-cover user-photo" 
                                     :class="view === 'list' ? 'w-16 h-16' : 'w-20 h-20 sm:w-24 sm:h-24'" 
                                     src="{{ Storage::url($photoPath) }}" 
                                     alt="{{ __('app.profile_photo_alt', ['name' => $user->name . ' ' . $user->surname]) }}"
                                     @contextmenu.prevent
                                     draggable="false">
                            @else
                                <div class="rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center"
                                     :class="view === 'list' ? 'w-16 h-16' : 'w-20 h-20 sm:w-24 sm:h-24'"
                                     role="img"
                                     aria-label="{{ __('app.no_profile_photo_alt', ['name' => $user->name . ' ' . $user->surname]) }}">
                                    <span class="text-lg sm:text-2xl font-bold text-gray-600 dark:text-gray-300">{{ $user->initials() }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1" :class="view === 'grid' ? 'text-center mt-4' : ''">
                            <h3 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-100">
                                {{ $user->name }} {{ $user->surname }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-1">{{ $user->title_name ?? '—' }} - {{ $user->department_name ?? '—' }}</p>
                            <div class="text-sm text-gray-700 dark:text-gray-200 space-y-1">
                                <p>📞 {{ $user->phone ?? '—' }}</p>
                                <p>✉️ {{ $user->email ?? '—' }}</p>
                            </div>
                            <div class="mt-3 sm:mt-4 flex justify-end">
                                <a 
                                    href="{{ route('admin.users.edit', $user->id) }}" 
                                    class="inline-flex items-center px-3 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition text-sm"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    {{ __('app.edit') }}
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="mt-6" role="navigation" aria-label="{{ __('app.pagination') }}">
                {{ $users->links() }}
            </div>
        </section>
    @elseif($searched)
        <div class="text-center text-gray-500 dark:text-gray-400 py-10" role="status">
            <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('app.no_results_found') }}</h3>
            <p class="text-gray-500 dark:text-gray-400">{{ __('app.change_search_try_again') }}</p>
        </div>
    @else
        <div class="text-center text-gray-500 dark:text-gray-400 py-10" role="status">
            <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('app.search_users') }}</h3>
            <p class="text-gray-500 dark:text-gray-400">{{ __('app.search_to_list_users') }}</p>
        </div>
    @endif
</div>
