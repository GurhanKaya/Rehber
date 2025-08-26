@props([
    'searchable' => true,
    'filters' => [],
    'showAdvanced' => false,
    'showViewOptions' => false,
    'showFilterToggle' => true
])

<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
    <div class="p-4">
        <!-- Search Bar -->
        @if($searchable)
            <div class="mb-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        placeholder="Ara..." 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        wire:model.live.debounce.300ms="query"
                    >
                </div>
            </div>
        @endif

        <!-- Filters Row -->
        @if(!empty($filters) || $showAdvanced)
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <!-- Basic Filters -->
                @if(!empty($filters))
                    <div class="flex flex-wrap gap-2">
                        @foreach($filters as $filter)
                            @if($filter['type'] === 'select')
                                <div class="flex flex-col">
                                    <label class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ $filter['label'] }}
                                    </label>
                                    <select 
                                        class="border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                        wire:model.live="{{ $filter['model'] }}"
                                    >
                                        <option value="">{{ $filter['placeholder'] ?? 'Seçiniz' }}</option>
                                        @foreach($filter['options'] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif($filter['type'] === 'checkbox')
                                <div class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        id="{{ $filter['id'] }}"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        wire:model.live="{{ $filter['model'] }}"
                                    >
                                    <label for="{{ $filter['id'] }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $filter['label'] }}
                                    </label>
                                </div>
                            @elseif($filter['type'] === 'date')
                                <div class="flex flex-col">
                                    <label class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ $filter['label'] }}
                                    </label>
                                    <input 
                                        type="date" 
                                        class="border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                        wire:model.live="{{ $filter['model'] }}"
                                    >
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    @if($showFilterToggle)
                        <button 
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            wire:click="toggleFilters"
                        >
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            {{ $showFilters ? 'Filtreleri Gizle' : 'Filtreleri Göster' }}
                        </button>
                    @endif

                    @if($searched ?? false)
                        <button 
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            wire:click="clearFilters"
                        >
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Temizle
                        </button>
                    @endif

                    <button 
                        type="button"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        wire:click="search"
                    >
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Ara
                    </button>
                </div>
            </div>
        @endif

        <!-- Advanced Filters Panel -->
        @if($showAdvanced && ($showFilters ?? false))
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                {{ $advancedFilters ?? '' }}
            </div>
        @endif

        <!-- View Options -->
        @if($showViewOptions)
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Görünüm:</span>
                        <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                            <button 
                                type="button"
                                class="px-3 py-1 text-sm font-medium rounded-md {{ ($viewMode ?? 'grid') === 'grid' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}"
                                wire:click="$set('viewMode', 'grid')"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                            </button>
                            <button 
                                type="button"
                                class="px-3 py-1 text-sm font-medium rounded-md {{ ($viewMode ?? 'grid') === 'list' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}"
                                wire:click="$set('viewMode', 'list')"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $items->total() ?? 0 }} sonuç bulundu
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
