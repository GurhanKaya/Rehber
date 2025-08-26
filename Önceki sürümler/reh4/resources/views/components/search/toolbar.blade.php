@props([
    'queryModel' => 'query',
    'searchAction' => 'search',
    'toggleFiltersAction' => 'toggleFilters',
    'clearAction' => 'clearSearch',
    'selectedTitleModel' => 'selectedTitle',
    'selectedDepartmentModel' => 'selectedDepartment',
    'hasPhoneModel' => 'hasPhone',
    'hasEmailModel' => 'hasEmail',
    'hasPhoneValue' => false,
    'hasEmailValue' => false,
    'titles' => [],
    'departments' => [],
    'showClear' => false,
    'showAdvanced' => true,
    'showViewOptions' => true,
    'showFilterToggle' => true,
])

<section class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 sm:p-6 mb-6 space-y-4" role="search" aria-label="{{ __('app.filters') }}">
    <form wire:submit.prevent="{{ $searchAction }}" class="space-y-4">
        <!-- Search input and buttons -->
        <div class="flex flex-col lg:flex-row gap-4 items-stretch lg:items-center">
            <div class="flex-1">
                <label for="global-search" class="sr-only">{{ __('app.search_button') }}</label>
                <input
                    id="global-search"
                    type="text"
                    wire:model.debounce.500ms="{{ $queryModel }}"
                    placeholder="{{ __('app.search_placeholder') }}"
                    class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
            </div>
            <button
                type="submit"
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition flex items-center justify-center"
            >
                <span class="mr-2">{{ __('app.search_button') }}</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>
            @if($showClear)
                <button
                    type="button"
                    wire:click="{{ $clearAction }}"
                    class="px-4 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition flex items-center justify-center"
                >
                    <span class="mr-2">{{ __('app.clear') }}</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            @endif
            @if($showFilterToggle)
                <button 
                    type="button" 
                    wire:click="{{ $toggleFiltersAction }}" 
                    class="px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition flex items-center justify-center"
                    aria-controls="filter-panel"
                >
                    <svg class="w-5 h-5 mr-2 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    <span>{{ __('app.filters') }}</span>
                </button>
            @endif
        </div>

        <!-- Quick Filter button and view toggles -->
        <div class="flex items-center justify-between gap-4 py-1 w-full">
            <div class="flex flex-wrap gap-2 items-center"></div>

            @if($showViewOptions)
                <div class="flex space-x-2 ml-auto px-1" role="group" aria-label="View options">
                    <button type="button" x-on:click="view = 'grid'" class="p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition" :class="view === 'grid' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-gray-300'" :aria-pressed="view === 'grid'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h4v4H4V6zm6 0h4v4h-4V6zm6 0h4v4h-4V6M4 14h4v4H4v-4zm6 0h4v4h-4v-4zm6 0h4v4h-4v-4z" />
                        </svg>
                    </button>
                    <button type="button" x-on:click="view = 'wide'" class="p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition" :class="view === 'wide' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-gray-300'" :aria-pressed="view === 'wide'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h8M4 18h16" />
                        </svg>
                    </button>
                    <button type="button" x-on:click="view = 'list'" class="p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition" :class="view === 'list' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-gray-300'" :aria-pressed="view === 'list'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            @endif
        </div>

        <!-- Advanced filters -->
        @if(isset($advanced))
            {{ $advanced }}
        @elseif($showAdvanced)
            <div x-show="open" x-transition class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 bg-gray-50 dark:bg-zinc-900 p-4 rounded-lg" id="filter-panel">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.department') }}</label>
                    <select wire:model="{{ $selectedDepartmentModel }}" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">{{ __('app.all_departments') }}</option>
                        @foreach($departments as $d)
                            <option value="{{ $d }}">{{ $d }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.title') }}</label>
                    <select wire:model="{{ $selectedTitleModel }}" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">{{ __('app.all_titles') }}</option>
                        @foreach($titles as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.contact') }}</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="{{ $hasPhoneModel }}" class="rounded border-gray-300 text-blue-600 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('app.has_phone') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="{{ $hasEmailModel }}" class="rounded border-gray-300 text-blue-600 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('app.has_email') }}</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-end">
                    <button type="button" wire:click="{{ $clearAction }}" class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        {{ __('app.reset') }}
                    </button>
                </div>
            </div>
        @endif
    </form>
</section>


