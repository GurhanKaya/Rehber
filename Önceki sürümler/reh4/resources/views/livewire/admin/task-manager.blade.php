@php
$statuses = ['' => __('app.all_statuses'), 'bekliyor' => __('app.waiting'), 'devam ediyor' => __('app.in_progress'), 'tamamlandı' => __('app.completed_status'), 'iptal' => __('app.cancelled_status')];
$types = ['' => __('app.all_types'), 'public' => __('app.open_task'), 'private' => __('app.private_task'), 'cooperative' => __('app.cooperative_task')];
@endphp

<div class="w-full max-w-7xl mx-auto" x-data="{ open: @entangle('showFilters') }" role="main" aria-label="{{ __('app.task_management_aria') }}">
    
    <!-- Header Section -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ __('app.task_management') }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('app.view_edit_manage_all_tasks') }}</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('admin.tasks.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg shadow-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ __('app.add_new_task') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filters Section -->
    <section class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 sm:p-6 mb-6 space-y-6" role="search" aria-label="{{ __('app.task_search_filter_aria') }}">
        <x-search.toolbar 
            :show-clear="$searched || $status || $type"
            query-model="query"
            search-action="search"
            toggle-filters-action="toggleFilters"
            clear-action="clearSearch"
            :show-advanced="false"
            :show-view-options="false"
            :show-filter-toggle="true"
        />
        <div class="flex justify-end items-center">
            <div class="hidden sm:flex space-x-4 text-sm text-gray-600 dark:text-gray-400">
                <span>{{ __('app.total') }}: <strong class="text-gray-900 dark:text-gray-100">{{ $tasks->total() }}</strong></span>
            </div>
        </div>

        <!-- Filter Panel -->
        <div 
            x-show="open" 
            x-transition
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 bg-gray-50 dark:bg-zinc-900 p-4 rounded-lg"
            id="filter-panel"
            role="group"
            aria-label="{{ __('app.advanced_filter_options_aria') }}"
        >
            <div>
                <label for="type-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.type') }}</label>
                <select 
                    id="type-filter"
                    wire:model="type" 
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    aria-label="{{ __('app.task_type_filter_aria') }}"
                >
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.status') }}</label>
                <select 
                    id="status-filter"
                    wire:model="status" 
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    aria-label="{{ __('app.status_filter_aria') }}"
                >
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </section>

    <!-- Tasks Grid -->
    <section aria-label="{{ __('app.task_list_aria') }}" role="region">
        @if($tasks->count() > 0)
            <div class="sr-only" aria-live="polite">{{ $tasks->count() }} görev bulundu</div>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                @foreach($tasks as $task)
                    <article class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-md p-5 flex flex-col h-full transition hover:shadow-lg focus-within:ring-2 focus-within:ring-blue-500">
                        <header class="flex items-start justify-between mb-3">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 leading-tight">{{ $task->title }}</h3>
                            <span class="ml-2 px-2 py-1 rounded-full text-xs font-semibold shrink-0
                                @if($task->type=='public') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @elseif($task->type=='private') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                @elseif($task->type=='cooperative') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif"
                                role="badge"
                                aria-label="Görev türü: {{ $task->type }}">
                                @if($task->type == 'public')
                                    {{ __('app.open_task') }}
                                @elseif($task->type == 'private')
                                    {{ __('app.private_task') }}
                                @elseif($task->type == 'cooperative')
                                    {{ __('app.cooperative_task') }}
                                @else
                                    {{ ucfirst($task->type) }}
                                @endif
                            </span>
                        </header>
                        
                        <div class="mb-3 text-sm text-gray-600 dark:text-gray-300 line-clamp-3 flex-1">
                            {{ $task->description ?: __('app.no_description_available') }}
                        </div>
                        
                        <div class="space-y-2 text-xs text-gray-500 dark:text-gray-400 mt-auto">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span><strong>{{ __('app.assigned_to') }}:</strong> 
                                    @if($task->assignedUser && is_object($task->assignedUser) && isset($task->assignedUser->name) && isset($task->assignedUser->surname))
                                        {{ $task->assignedUser->name }} {{ $task->assignedUser->surname }}
                                    @else
                                        {{ __('app.unassigned') }}
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span><strong>{{ __('app.deadline') }}:</strong> 
                                    @if($task->deadline)
                                        {{ \Carbon\Carbon::parse($task->deadline)->format('d.m.Y H:i') }}
                                        @if(\Carbon\Carbon::parse($task->deadline)->isPast() && $task->status != 'tamamlandı')
                                            <span class="text-red-500 font-semibold">({{ __('app.overdue') }})</span>
                                        @endif
                                    @else
                                        {{ __('app.not_specified') }}
                                    @endif
                                </span>
                            </div>
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
                        
                        <footer class="flex gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                            <a href="{{ route('admin.tasks.detail', $task->id) }}" 
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium transition text-center focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center justify-center gap-2"
                               aria-label="{{ $task->title }} görevinin detaylarını görüntüle">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                {{ __('app.view_details') }}
                            </a>
                        </footer>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6" role="navigation" aria-label="Sayfalama">
                {{ $tasks->links() }}
            </div>
        @else
            <div class="text-center py-12" role="status">
                <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                    @if($searched || $status || $type)
                        {{ __('app.no_results_found') }}
                    @else
                        {{ __('app.no_tasks_yet') }}
                    @endif
                </h3>
                <p class="text-gray-500 dark:text-gray-400">
                    @if($searched || $status || $type)
                        {{ __('app.change_search_try_again') }}
                    @else
                        {{ __('app.first_task_prompt') }}
                    @endif
                </p>
            </div>
        @endif
    </section>
</div>