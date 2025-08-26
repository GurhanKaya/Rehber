@php 
$statuses = ['' => __('app.all_statuses'), 'bekliyor' => __('app.waiting'), 'devam ediyor' => __('app.in_progress'), 'tamamlandı' => __('app.completed_status'), 'iptal' => __('app.cancelled_status')];
$types = ['' => __('app.all_statuses'), 'public' => __('app.open_type'), 'private' => __('app.private_type'), 'cooperative' => __('app.cooperative_type')];
$priorities = ['' => __('app.all_statuses'), 'low' => __('app.priority_low'), 'medium' => __('app.priority_medium'), 'high' => __('app.priority_high'), 'urgent' => __('app.priority_urgent')];
@endphp

<div class="w-full max-w-6xl mx-auto mt-4 sm:mt-8" role="main" aria-label="{{ __('app.task_list_aria') }}" x-data="{ open: @entangle('showFilters') }">
    <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg shadow p-4 sm:p-6">
        <!-- Header with Açık Görevler Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
             <header>
                 <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('app.tasks_heading') }}</h1>
                 <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('app.tasks_subheading') }}</p>
             </header>
             <a href="{{ route('personel.public-tasks') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition font-semibold">
                 {{ __('app.open_tasks') }}
             </a>
        </div>
        
        <!-- Filtreler Alanı -->
        <section class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 sm:p-6 mb-6 space-y-6" role="search" aria-label="{{ __('app.task_search_filter_aria') }}">
            <x-search.toolbar 
                :show-clear="$searched || $status || $type || $onlyDeadlineToday || $onlyMyTasks"
                query-model="query"
                search-action="search"
                toggle-filters-action="toggleFilters"
                clear-action="clearSearch"
                :show-advanced="false"
                :show-view-options="false"
                :show-filter-toggle="true"
            />

            <!-- Hızlı filtre butonları -->
            <div class="flex flex-wrap gap-2 items-center">
                <button type="button" wire:click="filterDeadlineToday" class="px-4 py-2 rounded-lg font-semibold transition @if($onlyDeadlineToday) bg-orange-600 text-white @else bg-gray-200 dark:bg-zinc-700 dark:text-white @endif">{{ __('app.today_tasks') }}</button>
                
                <button type="button" wire:click="filterMyTasks" class="px-4 py-2 rounded-lg font-semibold transition @if($onlyMyTasks) bg-purple-600 text-white @else bg-gray-200 dark:bg-zinc-700 dark:text-white @endif">{{ __('app.only_mine') }}</button>
            </div>

            <!-- Detaylı Filtre Alanları -->
            <div 
                x-show="open" 
                x-transition 
                class="grid grid-cols-1 md:grid-cols-3 gap-4"
                id="filter-panel"
                role="group"
                aria-label="{{ __('app.advanced_filter_options_aria') }}"
            >
                <div>
                    <label for="status-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.status') }}</label>
                    <select 
                        id="status-filter"
                        wire:model="status" 
                        class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        aria-label="Durum filtresi"
                    >
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="type-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.type') }}</label>
                    <select 
                        id="type-filter"
                        wire:model="type" 
                        class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        aria-label="Tür filtresi"
                    >
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>


            </div>
        </section>

            <section aria-label="{{ __('app.task_list_aria') }}" role="region">
            @if($tasks->count() > 0)
                <div class="sr-only" aria-live="polite">{{ $tasks->count() }} görev bulundu</div>
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4 lg:gap-6">
                    @foreach($tasks as $task)
                        <article class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-md p-4 sm:p-5 flex flex-col h-full transition hover:shadow-lg focus-within:ring-2 focus-within:ring-blue-500">
                            <header class="flex items-start justify-between mb-3">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 leading-tight">{{ $task->title }}</h3>
                                <span class="ml-2 px-2 py-1 rounded text-xs font-semibold shrink-0
                                    @if($task->type=='public') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($task->type=='private') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                    @elseif($task->type=='cooperative') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif"
                                    role="badge"
                                    aria-label="Görev türü: {{ $task->type }}">
                                    @if($task->type == 'cooperative')
                                        {{ __('app.cooperative') }}
                                    @elseif($task->type == 'public')
                                        {{ __('app.open') }}
                                    @elseif($task->type == 'private')
                                        {{ __('app.private') }}
                                    @else
                                        {{ ucfirst($task->type) }}
                                    @endif
                                </span>
                            </header>
                            
                            <div class="mb-3 text-sm text-gray-600 dark:text-gray-300 line-clamp-3 flex-1">
                                {{ $task->description }}
                            </div>
                            
                            <!-- Task Creator/Assigner Info -->
                            @if($task->type == 'cooperative' && $task->creator)
                                <div class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="font-medium">{{ __('app.created_by') }}:</span> {{ $task->creator->name }} {{ $task->creator->surname }}
                                </div>
                            @endif

                            <!-- Participants Info for Cooperative Tasks -->
                            @if($task->type == 'cooperative' && $task->participants->count() > 0)
                                <div class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="font-medium">{{ __('app.participants') }}:</span> 
                                    {{ $task->participants->pluck('name')->join(', ') }}
                                </div>
                            @endif
                            
                            <div class="space-y-2 text-xs text-gray-500 dark:text-gray-400 mt-auto">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span><strong>{{ __('app.deadline') }}:</strong> {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d.m.Y H:i') : __('app.not_specified') }}</span>
                                </div>
                                @if($task->creator)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    <span><strong>{{ __('app.created_by') }}:</strong> {{ $task->creator->name }} {{ $task->creator->surname }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center">
                                    <div class="w-2 h-2 rounded-full mr-2 
                                        @if($task->status == 'tamamlandı') bg-green-500
                                        @elseif($task->status == 'devam ediyor') bg-yellow-500
                                        @elseif($task->status == 'iptal') bg-red-500
                                        @else bg-gray-500 @endif"
                                        aria-hidden="true"></div>
                                    <span><strong>{{ __('app.status') }}:</strong>
                                        @if($task->status == 'bekliyor')
                                            {{ __('app.waiting') }}
                                        @elseif($task->status == 'devam ediyor')
                                            {{ __('app.in_progress') }}
                                        @elseif($task->status == 'tamamlandı')
                                            {{ __('app.completed_status') }}
                                        @elseif($task->status == 'iptal')
                                            {{ __('app.cancelled_status') }}
                                        @else
                                            {{ ucfirst($task->status) }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            <footer class="mt-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                                <a 
                                    href="{{ route('personel.tasks.detail', $task->id) }}" 
                                    class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition text-center font-medium"
                                    aria-label="{{ __('app.view_details') }}: {{ $task->title }}"
                                >
                                    {{ __('app.view_details') }}
                                </a>
                            </footer>
                        </article>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($tasks->hasPages())
                    <div class="mt-6 flex justify-center">
                        {{ $tasks->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12" role="status">
                    <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                        @if($searched || $status || $type || $onlyDeadlineToday || $onlyMyTasks)
                            {{ __('app.no_results_found') }}
                        @else
                            {{ __('app.no_tasks_yet') }}
                        @endif
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        @if($searched || $status || $type || $onlyDeadlineToday || $onlyMyTasks)
                            {{ __('app.change_search_try_again') }}
                        @else
                            {{ __('app.no_assigned_tasks') }}
                        @endif
                    </p>
                </div>
            @endif
        </section>
    </div>
</div> 