<form method="POST" action="{{ route('logout') }}" class="inline">
    @csrf
    <button type="submit" {{ $attributes->merge(['class' => 'flex items-center bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition focus:outline-none focus:ring-2 focus:ring-red-500 text-sm xl:text-base']) }}>
        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
        </svg>
        <span>{{ __('auth.log_out') }}</span>
    </button>
</form>


