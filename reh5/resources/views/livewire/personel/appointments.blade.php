@php $statuses = ['' => __('app.all_statuses'), 'onaylandı' => __('app.approved_status'), 'ret' => __('app.rejected'), 'yapıldı' => __('app.completed_status')]; @endphp
<div class="w-full max-w-7xl mx-auto">
    <div class="border border-gray-200 dark:border-zinc-700 rounded-lg p-2 md:p-6 bg-white dark:bg-zinc-900">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('app.my_appointments') }}</h2>
        <a href="{{ route('personel.randevu-saatlerim') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition">{{ __('app.edit_appointment_slots') }}</a>
        </div>
        <div x-data="{ open: @entangle('showFilters') }">
            <!-- Filtreler Alanı -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6 mb-6 space-y-6">
                <x-search.toolbar 
                    :show-clear="$status || $onlyToday || $onlyPending"
                    query-model="query"
                    search-action="search"
                    toggle-filters-action="toggleFilters"
                    clear-action="clearSearch"
                    :show-advanced="false"
                    :show-view-options="false"
                    :show-filter-toggle="true"
                />

                <div class="flex flex-wrap gap-2 items-center">
                    <button type="button" wire:click="filterToday" class="px-4 py-2 rounded-lg font-semibold transition @if($onlyToday) bg-orange-600 text-white @else bg-gray-200 dark:bg-zinc-700 dark:text-white @endif">{{ __('app.today_appointments') }}</button>
                    <button type="button" wire:click="filterPending" class="px-4 py-2 rounded-lg font-semibold transition @if($onlyPending) bg-yellow-500 text-white @else bg-gray-200 dark:bg-zinc-700 dark:text-white @endif">{{ __('app.pending_approvals') }}</button>
                </div>

                <div x-show="open" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <select wire:model="status" class="px-4 py-3 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600 dark:text-white">
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

    @if($appointments && count($appointments))
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($appointments as $appointment)
                        <div class="bg-white dark:bg-zinc-800 shadow rounded-lg p-6 flex flex-col h-full">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-base font-semibold text-gray-700 dark:text-gray-200">{{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }}</span>
                                <span class="px-3 py-1 rounded text-sm font-semibold
                                    @if($appointment->status=='onaylandı') bg-green-200 text-green-800 dark:bg-green-700 dark:text-green-200
                                    @elseif($appointment->status=='ret') bg-red-200 text-red-800 dark:bg-red-700 dark:text-red-200
                                    @elseif($appointment->status=='yapıldı') bg-blue-200 text-blue-800 dark:bg-blue-700 dark:text-blue-200
                                    @else bg-yellow-200 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-200 @endif">
                                    @if($appointment->status=='bekliyor')
                                        {{ __('app.waiting') }}
                                    @elseif($appointment->status=='onaylandı')
                                        {{ __('app.approved_status') }}
                                    @elseif($appointment->status=='ret')
                                        {{ __('app.rejected') }}
                                    @elseif($appointment->status=='yapıldı')
                                        {{ __('app.completed_status') }}
                                    @else
                                        {{ ucfirst($appointment->status) }}
                                    @endif
                                </span>
                            </div>
                            <div class="mb-2">
                                <span class="inline-block text-base text-gray-700 dark:text-gray-200 font-semibold">
                                    @php $days = [__('app.sunday'), __('app.monday'), __('app.tuesday'), __('app.wednesday'), __('app.thursday'), __('app.friday'), __('app.saturday')]; @endphp
                                    {{ $appointment->appointmentSlot ? $days[$appointment->appointmentSlot->day_of_week] : '-' }}
                                </span>
                                <span class="inline-block ml-2 text-base text-gray-700 dark:text-gray-200 font-semibold">
                                    {{ $appointment->start_time ? ($appointment->start_time . ' - ' . $appointment->end_time) : '-' }}
                                </span>
                            </div>
                            <div class="flex-1 flex flex-col justify-center">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1">{{ $appointment->name }}</h3>
                                <p class="text-sm text-gray-700 dark:text-gray-200 mb-1">📞 {{ $appointment->phone ?? '—' }}</p>
                                <p class="text-sm text-gray-700 dark:text-gray-200 mb-1">✉️ {{ $appointment->email ?? '—' }}</p>
                            </div>
                            @if($appointment->status == 'bekliyor')
                                <div class="flex gap-2 mt-4">
                                    <button wire:click="approveAppointment({{ $appointment->id }})" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-semibold transition">{{ __('app.approve') }}</button>
                                    <button wire:click="rejectAppointment({{ $appointment->id }})" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-semibold transition">{{ __('app.reject') }}</button>
                                </div>
                            @elseif($appointment->status == 'onaylandı')
                                <div class="flex gap-2 mt-4">
                                    <button wire:click="rejectAppointment({{ $appointment->id }})" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-semibold transition">{{ __('app.reject') }}</button>
                                    <button wire:click="markAsDoneAppointment({{ $appointment->id }})" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition">{{ __('app.done') }}</button>
                                </div>
                            @elseif($appointment->status == 'ret')
                                <div class="grid grid-cols-2 gap-2 mt-4">
                                    <button wire:click="setPendingAppointment({{ $appointment->id }})" class="col-span-2 bg-yellow-500 hover:bg-yellow-600 text-white py-3 rounded-lg font-semibold transition">{{ __('app.set_pending') }}</button>
                                    <button wire:click="approveAppointment({{ $appointment->id }})" class="bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-semibold transition">{{ __('app.approve') }}</button>
                                    <button wire:click="deleteAppointment({{ $appointment->id }})" class="bg-red-600 hover:bg-red-800 text-white py-2 rounded-lg font-semibold transition">{{ __('app.delete') }}</button>
                                </div>
                            @elseif($appointment->status == 'yapıldı')
                                <div class="flex gap-2 mt-4">
                                    <button wire:click="deleteAppointment({{ $appointment->id }})" class="flex-1 bg-red-600 hover:bg-red-800 text-white py-2 rounded-lg font-semibold transition">{{ __('app.delete') }}</button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $appointments->links() }}
        </div>
    @else
                <div class="text-gray-500 dark:text-gray-400 text-center py-10">{{ __('app.no_appointments_yet') }}</div>
    @endif
        </div>
    </div>
</div>
