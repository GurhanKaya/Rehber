<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-2xl shadow-xl">
        <div class="px-8 py-6 border-b border-gray-200 dark:border-zinc-700">
            <div class="flex items-center gap-4">
                <div class="bg-blue-100 dark:bg-blue-900 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('app.edit_user_title') }}</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('app.update_profile_description') }}</p>
                </div>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="mx-8 mt-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <form wire:submit.prevent="update" class="p-8 space-y-8">
            <!-- Profile Photo Section -->
            <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('app.profile_photo') }}</h2>
                
                <div class="flex items-start gap-6">
                    <!-- Photo Display -->
                    <div class="shrink-0">
                        @if ($photo)
                            <div class="relative">
                                <img class="h-32 w-32 object-cover rounded-full border-4 border-white dark:border-zinc-700 shadow-lg" 
                                     src="{{ $photo->temporaryUrl() }}" alt="{{ __('app.profile_photo_preview') }}">
                                <div class="absolute -bottom-2 -right-2 bg-blue-500 rounded-full p-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        @elseif($user->photo)
                            <div class="relative">
                                <img class="h-32 w-32 object-cover rounded-full border-4 border-white dark:border-zinc-700 shadow-lg" 
                                     src="{{ Storage::url($user->photo) }}" alt="{{ __('app.profile_photo') }}">
                                <div class="absolute -bottom-2 -right-2 bg-green-500 rounded-full p-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                        @else
                            <div class="h-32 w-32 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center border-4 border-white dark:border-zinc-700 shadow-lg">
                                <span class="text-3xl font-bold text-white">{{ $user->initials() }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Photo Controls -->
                    <div class="flex-1 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('app.choose_new_photo') }}
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="file" wire:model="photo" accept="image/*"
                                       class="block w-full text-sm text-gray-500 dark:text-gray-300
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-lg file:border-0
                                              file:text-sm file:font-medium
                                              file:bg-blue-50 file:text-blue-700
                                              hover:file:bg-blue-100
                                              dark:file:bg-zinc-700 dark:file:text-white
                                              border border-gray-300 dark:border-zinc-600 rounded-lg p-2">
                            </div>
                            @error('photo') 
                                <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>
                        
                        @if($user->photo)
                            <button type="button" wire:click="deletePhoto"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/30 text-red-700 dark:text-red-300 text-sm font-medium rounded-lg transition-colors duration-150">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                {{ __('app.remove_current_photo') }}
                            </button>
                        @endif
                        
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            <p>{{ __('app.max_file_size') }}</p>
                            <p>{{ __('app.supported_formats') }}</p>
                            <p>{{ __('app.min_dimensions') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('app.personal_information') }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('app.name') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.defer="name" placeholder="{{ __('app.name_required') }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-zinc-700 dark:text-white @error('name') border-red-500 @enderror">
                        @error('name') 
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('app.surname') }}
                        </label>
                        <input type="text" wire:model.defer="surname" placeholder="{{ __('app.surname_field') }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-zinc-700 dark:text-white @error('surname') border-red-500 @enderror">
                        @error('surname') 
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('app.email') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="email" wire:model.defer="email" placeholder="{{ __('app.email_required') }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-zinc-700 dark:text-white @error('email') border-red-500 @enderror">
                        @error('email') 
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('app.phone') }}
                        </label>
                        <input type="tel" wire:model.defer="phone" placeholder="{{ __('app.phone_format') }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-zinc-700 dark:text-white @error('phone') border-red-500 @enderror">
                        @error('phone') 
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Professional Information Section -->
            <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('app.professional_information') }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('app.job_title') }}
                        </label>
                        <select wire:model.defer="title_id" class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-zinc-700 dark:text-white @error('title_id') border-red-500 @enderror">
                            <option value="">{{ __('app.all_titles') }}</option>
                            @foreach($titles as $id => $name)
                                <option value="{{ $id }}" {{ $title_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('title_id') 
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('app.department') }}
                        </label>
                        <select wire:model.defer="department_id" class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-zinc-700 dark:text-white @error('department_id') border-red-500 @enderror">
                            <option value="">{{ __('app.all_departments') }}</option>
                            @foreach($departments as $id => $name)
                                <option value="{{ $id }}" {{ $department_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('department_id') 
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('app.select_role') }} <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.defer="role"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-zinc-700 dark:text-white @error('role') border-red-500 @enderror">
                            <option value="">{{ __('app.select_role') }}</option>
                            <option value="admin">{{ __('app.admin_role') }}</option>
                            <option value="personel">{{ __('app.staff_role') }}</option>
                        </select>
                        @error('role') 
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Security Section -->
            <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('app.security') }}</h2>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('app.new_password_optional') }}
                    </label>
                    <input type="password" wire:model.defer="password" placeholder="{{ __('app.min_8_characters') }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-zinc-700 dark:text-white @error('password') border-red-500 @enderror">
                    @error('password') 
                        <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> 
                    @enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ __('app.password_change_note') }}
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-4 pt-6 border-t border-gray-200 dark:border-zinc-700">
                <button type="button" 
                        onclick="window.history.back()"
                        class="px-6 py-3 border border-gray-300 dark:border-zinc-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-gray-500 transition">
                    {{ __('app.cancel') }}
                </button>
                
                <button type="button" 
                        wire:click="delete"
                        onclick="return confirm('{{ __('app.are_you_sure_delete') }}')"
                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    {{ __('app.delete') }}
                </button>
                
                <button type="submit"
                        class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span wire:loading.remove>{{ __('app.update_information') }}</span>
                    <span wire:loading>{{ __('app.updating') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
