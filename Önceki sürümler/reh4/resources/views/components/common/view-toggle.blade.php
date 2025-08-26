@props([
    // Alpine state variable name to read/write view mode ('grid' | 'list' | 'wide')
    'viewVar' => 'view',
])

<div class="flex bg-gray-100 dark:bg-zinc-700 rounded-lg p-1" role="group" aria-label="View options">
    <button
        type="button"
        x-on:click="{{ $viewVar }} = 'grid'"
        class="px-3 py-2 rounded-md text-sm font-medium transition"
        :class="{{ $viewVar }} === 'grid' ? 'bg-white dark:bg-zinc-600 text-gray-900 dark:text-gray-100 shadow' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
        aria-label="Grid view"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
        </svg>
    </button>
    <button
        type="button"
        x-on:click="{{ $viewVar }} = 'list'"
        class="px-3 py-2 rounded-md text-sm font-medium transition"
        :class="{{ $viewVar }} === 'list' ? 'bg-white dark:bg-zinc-600 text-gray-900 dark:text-gray-100 shadow' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
        aria-label="List view"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
        </svg>
    </button>
</div>



