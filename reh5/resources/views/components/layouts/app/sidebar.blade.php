@props(['title' => null])

<div class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 hidden md:flex md:flex-col">
            <div class="px-4 py-4 border-b border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.home') }}" class="text-lg font-bold">{{ __('app.admin_panel') }}</a>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="{{ route('admin.users') }}" class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('app.users') }}</a>
                <a href="{{ route('admin.departments') }}" class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('app.departments') }}</a>
                <a href="{{ route('admin.titles') }}" class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('app.titles') }}</a>
                <a href="{{ route('admin.tasks') }}" class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('app.tasks') }}</a>
                <a href="{{ route('admin.appointments') }}" class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('app.appointments') }}</a>
                <a href="{{ route('admin.appointment.slots') }}" class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('app.appointment_slots') }}</a>
                <a href="{{ route('admin.profile.edit') }}" class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('app.profile') }}</a>
            </nav>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col">
            <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 py-4">
                    <h1 class="text-xl font-semibold">{{ $title }}</h1>
                </div>
            </header>
            <main class="flex-1 max-w-7xl mx-auto w-full px-4 py-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</div>
