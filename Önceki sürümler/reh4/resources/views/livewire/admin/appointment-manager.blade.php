@php
$statuses = ['' => __('app.all_statuses'), 'bekliyor' => __('app.waiting'), 'onaylandı' => __('app.approved_status'), 'ret' => __('app.rejected'), 'yapıldı' => __('app.completed_status')];
$dateOptions = ['' => __('app.all_statuses'), 'today' => __('app.today'), 'tomorrow' => __('app.tomorrow'), 'this_week' => __('app.this_week'), 'this_month' => __('app.this_month')];
@endphp

<div class="w-full max-w-7xl mx-auto" x-data="{ open: @entangle('showFilters') }" role="main" aria-label="{{ __('app.appointment_management_aria') }}">
    
    <!-- Header Section -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ __('app.appointment_management') }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('app.view_edit_manage_all_tasks') }}</p>
            </div>
            <a href="{{ route('admin.appointment.slots') }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-semibold rounded-lg shadow-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ __('app.appointment_slots') }}
            </a>
        </div>
    </div>

    <!-- Search and Filters Section -->
    <section class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 sm:p-6 mb-6 space-y-6" role="search" aria-label="{{ __('app.appointment_search_filter_aria') }}">
        <x-search.toolbar 
            :show-clear="$hasSearched || $statusFilter || $userFilter || $dateFilter"
            query-model="search"
            search-action="searchAppointments"
            toggle-filters-action="toggleFilters"
            clear-action="clearFilters"
            :show-advanced="false"
            :show-view-options="false"
            :show-filter-toggle="true"
        />
        <div class="flex justify-end items-center">
            <div class="hidden sm:flex space-x-4 text-sm text-gray-600 dark:text-gray-400">
                <span>{{ __('app.total') }}: <strong class="text-gray-900 dark:text-gray-100">{{ $appointments->total() }}</strong></span>
            </div>
        </div>

            <!-- Filter Panel -->
            <div 
                x-show="open" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 dark:bg-zinc-900 p-4 rounded-lg"
                id="filter-panel"
                role="group"
                aria-label="Gelişmiş filtre seçenekleri"
            >
                <div>
                    <label for="status-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.status') }}</label>
                    <select 
                        id="status-filter"
                        wire:model="statusFilter" 
                        class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        aria-label="Durum filtresi"
                    >
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="user-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.staff') }}</label>
                    <select 
                        id="user-filter"
                        wire:model="userFilter" 
                        class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        aria-label="Personel filtresi"
                    >
                        <option value="">{{ __('app.all') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} {{ $user->surname }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="date-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.date') }}</label>
                    <select 
                        id="date-filter"
                        wire:model="dateFilter" 
                        class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        aria-label="Tarih filtresi"
                    >
                        @foreach($dateOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </section>

    <!-- Appointments Grid -->
    <section aria-label="{{ __('app.appointment_list_aria') }}" role="region">
        @if($appointments->count() > 0)
            <div class="sr-only" aria-live="polite">{{ __('app.users_found', ['count' => $appointments->count()]) }}</div>
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                @foreach($appointments as $appointment)
                    <article class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-md p-5 flex flex-col h-full transition hover:shadow-lg focus-within:ring-2 focus-within:ring-blue-500">
                        <header class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $appointment->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $appointment->user->name }} {{ $appointment->user->surname }}</p>
                            </div>
                            <span class="ml-2 px-3 py-1 rounded-full text-xs font-semibold shrink-0
                                @if($appointment->status=='bekliyor') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($appointment->status=='onaylandı') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($appointment->status=='ret') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @else bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @endif"
                                role="badge"
                                aria-label="Randevu durumu: {{ $appointment->status }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </header>
                        
                        <div class="space-y-3 text-sm text-gray-600 dark:text-gray-300 flex-1">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span><strong>{{ __('app.date') }}:</strong> {{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }}</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span><strong>{{ __('app.time') }}:</strong> {{ $appointment->start_time }} - {{ $appointment->end_time }}</span>
                            </div>
                            @if($appointment->phone)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span>{{ $appointment->phone }}</span>
                                </div>
                            @endif
                            @if($appointment->email)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="truncate">{{ $appointment->email }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Quick Status Actions -->
                        @if($appointment->status == 'bekliyor')
                            <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                                <button wire:click="updateStatus({{ $appointment->id }}, 'onaylandı')" 
                                        class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-medium transition focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-sm">
                                    {{ __('app.approve') }}
                                </button>
                                <button wire:click="updateStatus({{ $appointment->id }}, 'ret')" 
                                        class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-medium transition focus:ring-2 focus:ring-red-500 focus:ring-offset-2 text-sm">
                                    {{ __('app.reject') }}
                                </button>
                            </div>
                        @endif
                        
                        <footer class="flex gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                            <a href="{{ route('admin.appointments.edit', $appointment) }}" 
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium transition focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm text-center">
                                {{ __('app.edit') }}
                            </a>
                            <button wire:click="deleteAppointment({{ $appointment->id }})" 
                                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                                    onclick="return confirm('{{ __('app.are_you_sure_delete') }}')"
                                    aria-label="Randevuyu sil">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </footer>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6" role="navigation" aria-label="Sayfalama">
                {{ $appointments->links() }}
            </div>
        @else
            <div class="text-center py-12" role="status">
                <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                    @if($hasSearched || $statusFilter || $userFilter || $dateFilter)
                        {{ __('app.no_results_found') }}
                    @else
                        {{ __('app.no_appointments_yet') }}
                    @endif
                </h3>
                <p class="text-gray-500 dark:text-gray-400">
                    @if($hasSearched || $statusFilter || $userFilter || $dateFilter)
                        {{ __('app.change_search_try_again') }}
                    @else
                        {{ __('app.first_appointment_prompt') }}
                    @endif
                </p>
            </div>
        @endif
    </section>

    <!-- Appointment Modal -->
    {{-- Modal ve ilgili kodlar tamamen kaldırıldı --}}
</div>
