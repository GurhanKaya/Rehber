@php
$days = [__('app.sunday'), __('app.monday'), __('app.tuesday'), __('app.wednesday'), __('app.thursday'), __('app.friday'), __('app.saturday')];
@endphp

<div class="w-full max-w-6xl mx-auto" x-data="{ open: @entangle('showFilters') }" role="main" aria-label="{{ __('app.appointment_slots_management_aria') }}">
    
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-6 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-3 rounded-lg">
            <div class="whitespace-pre-line font-mono text-sm">{{ session('error') }}</div>
        </div>
    @endif
    
    <!-- Header Section -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ __('app.appointment_slots_management') }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('app.view_and_edit_personel_slots') }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.appointments') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ __('app.back_to_appointments') }}
                </a>
                <button wire:click="showCreateModal" 
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg shadow-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ __('app.add_new_slot') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Search and Filters Section -->
    <section class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 sm:p-6 mb-6 space-y-6" role="search" aria-label="{{ __('app.appointment_slots_search_aria') }}">
        <x-search.toolbar 
            :show-clear="$hasSearched || $selectedUser || $selectedDay !== ''"
            query-model="search"
            search-action="searchSlots"
            toggle-filters-action="toggleFilters"
            clear-action="clearFilters"
            :show-advanced="false"
            :show-view-options="false"
            :show-filter-toggle="true"
        />
        <div class="flex justify-end items-center">
            <div class="hidden sm:flex space-x-4 text-sm text-gray-600 dark:text-gray-400">
                <span>Toplam: <strong class="text-gray-900 dark:text-gray-100">{{ $slots->count() }}</strong></span>
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
                    <label for="user-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.staff') }}</label>
                    <select 
                        id="user-filter"
                        wire:model="selectedUser" 
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
                    <label for="day-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.day') }}</label>
                    <select 
                        id="day-filter"
                        wire:model="selectedDay" 
                        class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        aria-label="Gün filtresi"
                    >
                        <option value="">{{ __('app.all') }}</option>
                        @foreach($days as $key => $day)
                            <option value="{{ $key }}">{{ $day }}</option>
                        @endforeach
                    </select>
                </div>


            </div>
    </section>

    <!-- Slots Grid -->
    @if($slots->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($slots as $slot)
                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-md p-5 transition hover:shadow-lg">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                {{ $slot->user->name }} {{ $slot->user->surname }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $days[$slot->day_of_week] }}</p>
                        </div>

                    </div>
                    
                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>
                                <strong>{{ __('app.time') }}:</strong> {{ $slot->start_time }} - {{ $slot->end_time }}

                            </span>
                        </div>
                    </div>
                    
                    <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                        <button wire:click="showEditModal({{ $slot->id }})" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium transition text-sm">
                            {{ __('app.edit') }}
                        </button>
                        <button wire:click="deleteSlot({{ $slot->id }})" 
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                                onclick="return confirm('{{ __('app.are_you_sure_delete') }}')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                @if($hasSearched)
                    {{ __('app.no_slots_match_criteria') }}
                @else
                    {{ __('app.no_slots_added_yet') }}
                @endif
            </h3>
            <p class="text-gray-500 dark:text-gray-400">
                @if($hasSearched)
                    {{ __('app.try_different_search_criteria') }}.
                @else
                    {{ __('app.first_slot_prompt') }}
                @endif
            </p>
        </div>
    @endif

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-xl w-full max-w-md">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                        {{ $editId ? __('app.edit') . ' ' . __('app.appointment_slots') : __('app.add_new_slot') }}
                    </h2>
                    
                    <form x-on:submit.prevent="$wire.saveSlot()" class="space-y-4">
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.staff') }} *</label>
                            <select wire:model="user_id" id="user_id" class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white" required @if($editId) disabled @endif>
                                <option value="">{{ __('app.select_staff') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} {{ $user->surname }}</option>
                                @endforeach
                            </select>
                            @error('user_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>



                        <div>
                            <label for="day_of_week" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.day') }} *</label>
                            <select wire:model.defer="day_of_week" id="day_of_week" class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white" required>
                                <option value="">{{ __('app.select_day') }}</option>
                                @foreach($days as $index => $day)
                                    <option value="{{ $index }}">{{ $day }}</option>
                                @endforeach
                            </select>
                            @error('day_of_week') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.start_time') }} *</label>
                                <input type="time" wire:model.defer="start_time" id="start_time" class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white" required>
                                @error('start_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.end_time') }} *</label>
                                <input type="time" wire:model.defer="end_time" id="end_time" class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white" required>
                                @error('end_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>



                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 border rounded-lg text-gray-700 dark:text-gray-300">{{ __('app.cancel') }}</button>
                            @if($editId)
                                <button type="button" wire:click="deleteSlot({{ $editId }})" class="px-4 py-2 bg-red-600 text-white rounded-lg" onclick="return confirm('{{ __('app.are_you_sure_delete') }}')">{{ __('app.delete') }}</button>
                            @endif
                            <button type="button" wire:click="saveSlot" class="px-6 py-2 bg-green-600 text-white rounded-lg">{{ $editId ? __('app.update') : __('app.create') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
