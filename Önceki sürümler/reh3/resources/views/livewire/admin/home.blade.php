<div class="w-full max-w-7xl mx-auto p-6 space-y-6">
    <!-- Header Section -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 p-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Admin Paneli</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">{{ Carbon\Carbon::now()->format('d F Y, l') }}</p>
            </div>
        </div>
    </div>

    <!-- Alerts Section -->
    @if($pendingAppointments > 0 || $todayDeadlineTasks > 0 || $pendingTasks > 0)
        <div class="space-y-3">
            @if($pendingAppointments > 0)
                <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                {{ $pendingAppointments }} bekleyen randevu var
                            </h3>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                <a href="{{ route('admin.appointments') }}" class="underline hover:no-underline">Randevuları görüntüle</a>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if($todayDeadlineTasks > 0)
                <div class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                {{ $todayDeadlineTasks }} görevin son tarihi bugün
                            </h3>
                            <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                                <a href="{{ route('admin.tasks') }}" class="underline hover:no-underline">Görevleri görüntüle</a>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if($pendingTasks > 0)
                <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                {{ $pendingTasks }} bekleyen görev var
                            </h3>
                            <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                                <a href="{{ route('admin.tasks') }}" class="underline hover:no-underline">Görevleri görüntüle</a>
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Kullanıcı İstatistikleri -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Toplam Kullanıcı</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalUsers }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $totalPersonel }} personel, {{ $totalAdmins }} admin
                    </p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Randevu İstatistikleri -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Toplam Randevu</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalAppointments }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $pendingAppointments }} bekliyor, {{ $todayAppointments }} bugün
                    </p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Görev İstatistikleri -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Toplam Görev</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalTasks }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $pendingTasks }} bekliyor, {{ $completedTasks }} tamamlandı
                    </p>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Yaklaşan Görevler -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Yaklaşan Görevler</h2>
                <a href="{{ route('admin.tasks') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Tümünü gör</a>
            </div>
            @if($upcomingTasks->count() > 0)
                <div class="space-y-3">
                    @foreach($upcomingTasks as $task)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-zinc-700 rounded-lg">
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $task->title }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Son tarih: {{ Carbon\Carbon::parse($task->deadline)->format('d.m.Y') }}
                                </p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($task->status == 'bekliyor') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($task->status == 'devam ediyor') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200 @endif">
                                {{ ucfirst($task->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">Yaklaşan görev yok</p>
            @endif
        </div>

        <!-- Son Randevular -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Son Randevular</h2>
                <a href="{{ route('admin.appointments') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Tümünü gör</a>
            </div>
            @if($recentAppointments->count() > 0)
                <div class="space-y-3">
                    @foreach($recentAppointments as $appointment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-zinc-700 rounded-lg">
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $appointment->name }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $appointment->appointmentSlot->user->name }} {{ $appointment->appointmentSlot->user->surname }} - 
                                    {{ Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }}
                                </p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($appointment->status == 'bekliyor') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($appointment->status == 'onaylandı') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($appointment->status == 'yapıldı') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">Son randevu yok</p>
            @endif
        </div>
    </div>

    <!-- Detailed Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Görev Detayları -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Görev Detayları</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Public Görevler</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $publicTasks }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">İş Birliği Görevleri</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $cooperativeTasks }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Atanmış Görevler</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $assignedTasks }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Devam Eden</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $inProgressTasks }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">İptal Edilen</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $cancelledTasks }}</span>
                </div>
            </div>
        </div>

        <!-- Randevu Detayları -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Randevu Detayları</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Bekleyen</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $pendingAppointments }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Onaylanan</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $approvedAppointments }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Tamamlanan</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $completedAppointments }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Bugünkü</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $todayAppointments }}</span>
                </div>
            </div>
        </div>
    </div>
</div> 