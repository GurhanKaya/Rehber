<div class="w-full max-w-4xl mx-auto p-6" role="main" aria-label="{{ __('app.appointment_edit_aria') }}">
    
    <!-- Header -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ __('app.edit_appointment') }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('app.update_appointment_info') }}</p>
            </div>
            <a href="{{ route('admin.appointments') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('app.back') }}
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Appointment Info Card -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('app.appointment_info') }}</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">{{ __('app.customer_info') }}</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('app.full_name') }}</label>
                        <input type="text" wire:model="name" 
                               class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('app.phone') }}</label>
                        <input type="text" wire:model="phone" 
                               class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500">
                        @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('app.email') }}</label>
                        <input type="email" wire:model="email" 
                               class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500">
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">{{ __('app.appointment_details') }}</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('app.staff_member') }}</label>
                        <select wire:model="user_id" 
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="">{{ __('app.select_staff_member') }}</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} {{ $user->surname }}</option>
                            @endforeach
                        </select>
                        @error('user_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('app.date') }}</label>
                        <input type="date" wire:model="date" 
                               class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500">
                        @error('date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('app.start_time') }}</label>
                            <input type="time" wire:model="start_time" 
                                   class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500">
                            @error('start_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('app.end_time') }}</label>
                            <input type="time" wire:model="end_time" 
                                   class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500">
                            @error('end_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('app.status') }}</label>
                        <select wire:model="status" 
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="bekliyor">{{ __('app.waiting') }}</option>
                            <option value="onaylandı">{{ __('app.approved_status') }}</option>
                            <option value="ret">{{ __('app.rejected') }}</option>
                            <option value="yapıldı">{{ __('app.completed_status') }}</option>
                        </select>
                        @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 p-6">
        <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
            <div class="flex gap-2">
                <button wire:click="updateAppointment" 
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    {{ __('app.update') }}
                </button>
                
                <button wire:click="deleteAppointment" 
                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                         onclick="return confirm('{{ __('app.are_you_sure_delete') }}')">
                    {{ __('app.delete') }}
                </button>
            </div>
            
            <div class="flex gap-2">
                @if($status == 'bekliyor')
                    <button wire:click="updateStatus('onaylandı')" 
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        {{ __('app.approve') }}
                    </button>
                    <button wire:click="updateStatus('ret')" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        {{ __('app.reject') }}
                    </button>
                @elseif($status == 'onaylandı')
                    <button wire:click="updateStatus('yapıldı')" 
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        {{ __('app.mark_as_completed') }}
                    </button>
                @endif
            </div>
        </div>
    </div>
</div> 