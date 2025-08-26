@props([
    'items' => collect(),
    'columns' => [],
    'actions' => [],
    'searchable' => true,
    'filterable' => true,
    'sortable' => true,
    'pagination' => true,
    'emptyMessage' => 'Veri bulunamadı.',
    'loading' => false
])

<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
    <!-- Search and Filters Bar -->
    @if($searchable || $filterable)
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row gap-4">
                @if($searchable)
                    <div class="flex-1">
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

                @if($filterable)
                    <div class="flex gap-2">
                        <button 
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            wire:click="toggleFilters"
                        >
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filtreler
                        </button>

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
                    </div>
                @endif
            </div>

            <!-- Filters Panel -->
            @if($showFilters ?? false)
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $filters ?? '' }}
                </div>
            @endif
        </div>
    @endif

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    @foreach($columns as $column)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            @if($sortable && isset($column['sortable']) && $column['sortable'])
                                <button 
                                    type="button"
                                    class="group inline-flex items-center hover:text-gray-700 dark:hover:text-gray-200"
                                    wire:click="sortBy('{{ $column['key'] }}')"
                                >
                                    {{ $column['label'] }}
                                    <svg class="ml-2 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </button>
                            @else
                                {{ $column['label'] }}
                            @endif
                        </th>
                    @endforeach

                    @if(!empty($actions))
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            İşlemler
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @if($loading)
                    <tr>
                        <td colspan="{{ count($columns) + (!empty($actions) ? 1 : 0) }}" class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="ml-2 text-gray-500 dark:text-gray-400">Yükleniyor...</span>
                            </div>
                        </td>
                    </tr>
                @elseif($items->isEmpty())
                    <tr>
                        <td colspan="{{ count($columns) + (!empty($actions) ? 1 : 0) }}" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            {{ $emptyMessage }}
                        </td>
                    </tr>
                @else
                    @foreach($items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            @foreach($columns as $column)
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if(isset($column['format']) && is_callable($column['format']))
                                        {{ $column['format']($item) }}
                                    @elseif(isset($column['key']))
                                        {{ data_get($item, $column['key']) }}
                                    @else
                                        {{ $column['label'] }}
                                    @endif
                                </td>
                            @endforeach

                            @if(!empty($actions))
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        @foreach($actions as $action)
                                            @if(isset($action['condition']) && !$action['condition']($item))
                                                @continue
                                            @endif

                                            <button 
                                                type="button"
                                                class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-{{ $action['color'] ?? 'blue' }}-700 bg-{{ $action['color'] ?? 'blue' }}-100 hover:bg-{{ $action['color'] ?? 'blue' }}-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $action['color'] ?? 'blue' }}-500"
                                                wire:click="{{ $action['action'] }}({{ $item->id }})"
                                            >
                                                @if(isset($action['icon']))
                                                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        {!! $action['icon'] !!}
                                                    </svg>
                                                @endif
                                                {{ $action['label'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($pagination && method_exists($items, 'links'))
        <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $items->links() }}
        </div>
    @endif
</div>
