<div class="w-full max-w-7xl mx-auto mt-6 sm:mt-10" role="main" aria-label="{{ __('personnel.dashboard_aria') }}">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ __('personnel.welcome_name', ['name' => auth()->user()->name]) }} 👋</h1>
        <p class="text-gray-600 dark:text-gray-400">{{ __('personnel.today_is_summary', ['date' => now()->format('d.m.Y')]) }}</p>
    </div>

    <!-- Bildirimler ve Uyarılar -->
    @if($pendingAppointments > 0 || $todayDeadlineTasks > 0 || $todayAppointments > 0)
        <div class="mb-8 space-y-3">
            @if($pendingAppointments > 0)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <div>
                            <p class="text-yellow-800 dark:text-yellow-200 font-medium">
                                {{ $pendingAppointments }} adet onaylanmamış randevunuz var
                            </p>
                            <a href="{{ route('personel.randevularim') }}" class="text-yellow-600 dark:text-yellow-400 text-sm hover:underline">
                                {{ __('personnel.view_appointments') }} →
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            @if($todayDeadlineTasks > 0)
                <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 p-4 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-red-800 dark:text-red-200 font-medium">
                                {{ $todayDeadlineTasks }} adet görevinizin bugün son tarihi
                            </p>
                            <a href="{{ route('personel.tasks') }}" class="text-red-600 dark:text-red-400 text-sm hover:underline">
                                {{ __('personnel.view_tasks') }} →
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            @if($todayAppointments > 0)
                <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <p class="text-blue-800 dark:text-blue-200 font-medium">
                                Bugün {{ $todayAppointments }} adet randevunuz var
                            </p>
                            <a href="{{ route('personel.randevularim') }}" class="text-blue-600 dark:text-blue-400 text-sm hover:underline">
                                Randevuları görüntüle →
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Bugünkü Randevular -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('personnel.today_appointments') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $todayAppointments }}</p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('personel.randevularim') }}" class="text-blue-600 dark:text-blue-400 text-sm hover:underline">
                    {{ __('personnel.view') }} →
                </a>
            </div>
        </div>

        <!-- Bekleyen Randevular -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('personnel.pending_appointments') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $pendingAppointments }}</p>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('personel.randevularim') }}" class="text-yellow-600 dark:text-yellow-400 text-sm hover:underline">
                    {{ __('personnel.approve') }} →
                </a>
            </div>
        </div>

        <!-- Public Atanmamış Görevler -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('personnel.public_tasks') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $publicUnassignedTasks }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('personnel.unassigned') }}</p>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('personel.public-tasks') }}" class="text-purple-600 dark:text-purple-400 text-sm hover:underline">
                    {{ __('personnel.view') }} →
                </a>
            </div>
        </div>

        <!-- Aktif Görevler -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('personnel.active_tasks') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $activeTasks }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('personnel.total_count', ['count' => $totalTasks]) }}</p>
                </div>
                <div class="bg-green-100 dark:bg-green-900 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('personel.tasks') }}" class="text-green-600 dark:text-green-400 text-sm hover:underline">
                    {{ __('personnel.view_tasks') }} →
                </a>
            </div>
        </div>
    </div>

    <!-- Ana İçerik Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Yaklaşan Görevler -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('personnel.upcoming_tasks') }}</h2>
                <a href="{{ route('personel.tasks') }}" class="text-blue-600 dark:text-blue-400 text-sm hover:underline">
                    {{ __('personnel.view_all') }} →
                </a>
            </div>
            
            @if($upcomingTasks->count() > 0)
                <div class="space-y-4">
                    @foreach($upcomingTasks as $task)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-700 rounded-lg">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900 dark:text-gray-100 text-sm">{{ $task->title }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ __('personnel.deadline_colon') }} {{ \Carbon\Carbon::parse($task->deadline)->format('d.m.Y H:i') }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($task->status == 'bekliyor') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif($task->status == 'devam ediyor') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200 @endif">
                                    {{ ucfirst($task->status) }}
                                </span>
                                <a href="{{ route('personel.tasks.detail', $task->id) }}" class="text-blue-600 dark:text-blue-400 text-xs hover:underline">
                                    {{ __('personnel.detail') }} →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('personnel.no_upcoming_tasks') }}</p>
                </div>
            @endif
        </div>

        <!-- Son Randevu Saatleri -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('personnel.recent_appointment_slots') }}</h2>
                <a href="{{ route('personel.randevu-saatlerim') }}" class="text-blue-600 dark:text-blue-400 text-sm hover:underline">
                    {{ __('personnel.view_all') }} →
                </a>
            </div>
            
            @if($recentSlots->count() > 0)
                <div class="space-y-4">
                    @foreach($recentSlots as $slot)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-700 rounded-lg">
                            <div class="flex-1">
                                @php $days = [__('app.sunday'), __('app.monday'), __('app.tuesday'), __('app.wednesday'), __('app.thursday'), __('app.friday'), __('app.saturday')]; @endphp
                                <h3 class="font-medium text-gray-900 dark:text-gray-100 text-sm">{{ $days[$slot->day_of_week] }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $slot->start_time }} - {{ $slot->end_time }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    {{ __('personnel.active') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('app.no_slots_added') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
