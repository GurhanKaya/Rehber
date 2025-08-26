<div class="max-w-3xl mx-auto bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 mt-10">
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-700 dark:to-blue-800 rounded-t-xl p-6 text-white">
        <h2 class="text-2xl font-bold text-center flex flex-col md:flex-row items-center justify-center gap-3">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            {{ __('app.book_appointment_for', ['name' => $user->name . ' ' . $user->surname]) }}
        </h2>
        <p class="text-center text-blue-100 mt-2 flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            {{ __('app.phone_security_note') }}
        </p>
    </div>
    
    <div class="p-6">

    @if($successMessage)
        <div class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 p-2 rounded mb-2 text-center">{{ $successMessage }}</div>
    @endif
    @if($errorMessage)
        <div class="bg-red-600 dark:bg-red-800 text-white p-2 rounded mb-2 text-center">{{ $errorMessage }}</div>
    @endif
    @if($infoMessage)
        <div class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 p-2 rounded mb-2 text-center font-semibold">
            {{ $infoMessage }}
        </div>
    @endif

    @if(count($slots) === 0)
        <div class="bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 p-4 rounded mb-4 text-center font-semibold">
            {{ __('app.no_slots_available') }}<br>
            {{ __('app.contact_info_message') }}.
        </div>
        <div class="text-center mb-6">
            @if($user->email)
                <div class="mb-1 text-gray-800 dark:text-white">{{ __('app.email_colon') }} <a href="mailto:{{ $user->email }}" class="text-blue-700 dark:text-blue-300 underline">{{ $user->email }}</a></div>
            @endif
            @if($user->phone)
                <div class="text-gray-800 dark:text-white">{{ __('app.phone_colon') }} <a href="tel:{{ $user->phone }}" class="text-blue-700 dark:text-blue-300 underline">{{ $user->phone }}</a></div>
            @endif
        </div>
    @else
        <form wire:submit.prevent="book" class="mb-6 space-y-6">
            <div class="space-y-4">
                <div class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    {{ __('app.contact') }}
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.full_name') }} *</label>
                        <input type="text" id="name" wire:model.defer="name" 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" 
                               placeholder="{{ __('app.your_name') }}" required>
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.phone') }}</label>
                        <input type="tel" id="phone" wire:model.defer="phone" 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" 
                               placeholder="{{ __('app.your_phone') }}"
                               maxlength="10"
                               pattern="[0-9]{10}"
                               title="{{ __('app.phone_validation_title') }}"
                               inputmode="numeric"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                               onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                    </div>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.email') }}</label>
                    <input type="email" id="email" wire:model.defer="email" 
                           class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" 
                           placeholder="{{ __('app.your_email') }}">
                </div>
            </div>
            <div class="mt-6">
                <div class="font-semibold mb-3 text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ __('app.date') }} {{ __('app.select_time') }}
                </div>
                @php
                    $days = ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi'];
                    $slotDays = collect($slots)->pluck('day_of_week')->unique()->sort()->values();
                    $minDate = \Carbon\Carbon::today();
                    $maxDate = $minDate->copy()->addDays(5); // Maksimum 5 gün sonrası
                    $validDates = [];
                    for ($d = $minDate->copy(); $d <= $maxDate; $d->addDay()) {
                        if ($slotDays->contains($d->dayOfWeek)) {
                            $validDates[] = $d->toDateString();
                        }
                    }
                @endphp
                <div class="flex gap-3">
                    <div class="flex-1">
                        <input type="date"
                            min="{{ $minDate->toDateString() }}"
                            max="{{ $maxDate->toDateString() }}"
                            value="{{ $selectedDate }}"
                            wire:change="selectDate($event.target.value)"
                            @if(count($validDates) === 0) disabled @endif
                            list="valid-dates"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <datalist id="valid-dates">
                            @foreach($validDates as $date)
                                <option value="{{ $date }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <button type="button" 
                            wire:click="findNextAvailableSlot"
                            class="px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition focus:ring-2 focus:ring-green-500 focus:ring-offset-2 flex items-center gap-2 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        {{ __('app.available_times') }}
                    </button>
                </div>
                @if($infoMessage)
                    <div class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 p-2 rounded mb-2 text-center font-semibold mt-4">
                        {{ $infoMessage }}
                    </div>
                @endif
                @if(!$selectedDate && $infoMessage)
                    <div class="text-center mb-6">
                        @if($user->email)
                            <div class="mb-1 text-gray-800 dark:text-white">{{ __('app.email_colon') }} <a href="mailto:{{ $user->email }}" class="text-blue-700 dark:text-blue-300 underline">{{ $user->email }}</a></div>
                        @endif
                        @if($user->phone)
                            <div class="text-gray-800 dark:text-white">{{ __('app.phone_colon') }} <a href="tel:{{ $user->phone }}" class="text-blue-700 dark:text-blue-300 underline">{{ $user->phone }}</a></div>
                        @endif
                    </div>
                @else
                    <div class="font-semibold mb-3 mt-6 text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('app.available_times') }}
                    </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $selectedDayOfWeek = null;
                            if ($selectedDate) {
                                $selectedDayOfWeek = \Carbon\Carbon::parse($selectedDate)->dayOfWeek;
                            }
                            @endphp
                        @if($selectedDayOfWeek !== null && isset($intervals[$selectedDayOfWeek]) && count($intervals[$selectedDayOfWeek]))
                            <div class="bg-gray-100 dark:bg-zinc-800 rounded p-3 border border-gray-300 dark:border-zinc-600">
                                <div class="font-semibold mb-2 text-gray-800 dark:text-gray-100">
                                    {{ $days[$selectedDayOfWeek] }} - {{ \Carbon\Carbon::parse($selectedDate)->format('d.m.Y') }}
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($intervals[$selectedDayOfWeek] as $interval)
                                        <button type="button"
                                            wire:click="$set('selectedInterval', '{{ $interval['slot_id'] }}|{{ $interval['start'] }}|{{ $interval['end'] }}')"
                                            @if($interval['conflict']) disabled @endif
                                            class="w-28 px-2 py-1 rounded-full transition-all duration-200 text-center select-none text-xs whitespace-nowrap font-semibold
                                                @if($interval['is_past']) bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 cursor-not-allowed border border-red-200 dark:border-red-800
                                                @elseif($interval['conflict']) bg-gray-200 dark:bg-zinc-700 text-gray-400 dark:text-gray-500 cursor-not-allowed
                                                @elseif($selectedInterval === ($interval['slot_id'].'|'.$interval['start'].'|'.$interval['end'])) bg-green-600 dark:bg-green-700 text-white shadow-lg ring-2 ring-green-800
                                                @else bg-blue-700 dark:bg-blue-800 text-white hover:bg-blue-800 dark:hover:bg-blue-900 cursor-pointer @endif">
                                            {{ $interval['start'] }} - {{ $interval['end'] }}
                                            @if($interval['is_past'])
                                                <div class="text-xs opacity-75">{{ __('app.past_time') }}</div>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 p-4 rounded text-center font-semibold col-span-2">
                                {{ __('app.no_available_times_for_this_day') }}
                            </div>
                        @endif
                </div>
                @endif
            </div>
            <div class="mt-8 text-center">
                <button type="submit" 
                        class="inline-flex items-center gap-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-4 rounded-lg text-lg font-semibold shadow-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="!selectedInterval || !name">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ __('app.book_appointment_button') }}
                </button>
            </div>
        </form>
    @endif
    </div>
</div>
