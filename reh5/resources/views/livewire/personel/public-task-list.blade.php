<div class="w-full max-w-5xl mx-auto mt-8" x-data="{ open: @entangle('showFilters') }">
    <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ __('app.open_tasks_opportunities') }}</h2>
        <x-search.toolbar 
            :show-clear="false"
            :show-advanced="true"
            :show-view-options="false"
            :show-filter-toggle="true"
            search-action="search"
            toggle-filters-action="toggleFilters"
            clear-action="clearSearch"
        >
            <x-slot name="advanced">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="filter-panel" x-show="open" x-transition>
                    <div class="md:col-span-2">
                        <div>
                            <div class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.type') }}</div>
                            <div class="flex gap-3" aria-label="{{ __('app.type') }}">
                                <button type="button" wire:click="toggleOpenType('public')" class="px-4 py-2 rounded-lg text-sm font-semibold transition @if($showPublic) bg-blue-600 text-white @else bg-white dark:bg-zinc-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 @endif" aria-pressed="{{ $showPublic ? 'true' : 'false' }}">{{ __('app.open') }}</button>
                                <button type="button" wire:click="toggleOpenType('cooperative')" class="px-4 py-2 rounded-lg text-sm font-semibold transition @if($showCooperative) bg-blue-600 text-white @else bg-white dark:bg-zinc-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 @endif" aria-pressed="{{ $showCooperative ? 'true' : 'false' }}">{{ __('app.cooperative') }}</button>
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block"></div>
                </div>
            </x-slot>
        </x-search.toolbar>
        @if(session('success'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 border border-green-300 dark:border-green-700">
                {{ session('success') }}
            </div>
        @endif
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($tasks as $task)
                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm hover:shadow-md p-5 flex flex-col h-full transition">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $task->title }}</span>
                        <span class="px-2 py-1 rounded text-xs font-semibold flex items-center gap-1
    @if($task->type=='public') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
    @elseif($task->type=='private') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
    @else bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 @endif">
    @if($task->type == 'cooperative')
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        {{ __('app.cooperative_task') }}
    @elseif($task->type == 'public')
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
        </svg>
        {{ __('app.open_task') }}
    @else
        {{ ucfirst($task->type) }}
    @endif
</span>
                    </div>
                    <div class="mb-2 text-sm text-gray-600 dark:text-gray-300 line-clamp-3">{{ $task->description }}</div>
                    <div class="flex-1"></div>
                    <div class="flex flex-col gap-1 text-xs text-gray-500 dark:text-gray-400 mt-2">
                        <div>{{ __('app.deadline') }}: <span class="font-semibold">{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d.m.Y H:i') : '-' }}</span></div>
                        @if($task->creator)
                            <div>{{ __('app.created_by') }}: <span class="font-semibold">{{ $task->creator->name }} {{ $task->creator->surname }}</span></div>
                        @endif
                    </div>
                    <button wire:click="assignToMe({{ $task->id }})" class="mt-4 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-semibold transition flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        @if($task->type == 'cooperative')
                            @if($task->participants->contains($userId))
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ __('app.joined') }}</span>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>{{ __('app.join_collaboration') }}</span>
                            @endif
                            <span class="ml-auto text-xs bg-white/20 rounded px-2 py-1">{{ __('app.participants_count', ['count' => $task->participants->count()]) }}</span>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>{{ __('app.take_task') }}</span>
                        @endif
                    </button>
                </div>
            @empty
                <div class="col-span-3 text-center text-gray-500 dark:text-gray-400 py-10">
                    <div class="flex flex-col items-center gap-3">
                        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <div>
                            <p class="text-lg font-semibold mb-2">{{ __('app.no_open_tasks') }}</p>
                            <p class="text-sm">{{ __('app.new_tasks_will_appear') }}</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($tasks->hasPages())
            <div class="mt-6">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
</div> 