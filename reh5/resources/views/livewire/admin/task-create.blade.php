<div class="w-full max-w-4xl mx-auto" role="main" aria-label="{{ __('app.new_task_create_aria') }}">
    
    <!-- Header Section -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ __('app.add_new_task') }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('app.add_new_task_sub') }}</p>
            </div>
            <a href="{{ route('admin.tasks') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('app.back') }}
            </a>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 p-6">
        <form wire:submit.prevent="saveTask" class="space-y-6">
            
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('app.task_title') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="title"
                       wire:model.defer="title" 
                       class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror" 
                       placeholder="{{ __('app.enter_task_title') }}" 
                       required>
                @error('title') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('app.description') }}
                </label>
                <textarea wire:model.defer="description" 
                          id="description"
                          rows="6"
                          class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror" 
                           placeholder="{{ __('app.enter_task_description') }}"></textarea>
                @error('description') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Type & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('app.task_type') }} <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.defer="type" 
                            id="type"
                            class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror">
                        <option value="public">{{ __('app.open') }}</option>
                        <option value="private">{{ __('app.private') }}</option>
                        <option value="cooperative">{{ __('app.cooperative') }}</option>
                    </select>
                    @error('type') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('app.status') }} <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.defer="status" 
                            id="status"
                            class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="bekliyor">{{ __('app.waiting') }}</option>
                        <option value="devam ediyor">{{ __('app.in_progress') }}</option>
                        <option value="tamamlandı">{{ __('app.completed_status') }}</option>
                        <option value="iptal">{{ __('app.cancelled_status') }}</option>
                    </select>
                    @error('status') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Assigned & Deadline -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{ taskType: @entangle('type') }">
                <div>
                    <label for="assigned_user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('app.assigned_to') }}
                        <span x-show="taskType === 'private'" class="text-red-500">*</span>
                    </label>
                    <select wire:model.defer="assigned_user_id" 
                            id="assigned_user_id"
                            class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('assigned_user_id') border-red-500 @enderror">
                        <option value="" x-text="taskType === 'private' ? '{{ __('app.select_staff_required') }}' : '{{ __('app.select_staff_optional') }}'"></option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} {{ $user->surname }}</option>
                        @endforeach
                    </select>
                    @error('assigned_user_id') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    <div x-show="taskType === 'private'" class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                        {{ __('app.private_task_requires_staff') }}
                    </div>
                </div>

                <div>
                    <label for="deadline" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('app.deadline') }}
                    </label>
                    <input type="datetime-local" 
                           id="deadline"
                           wire:model.defer="deadline" 
                           class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('deadline') border-red-500 @enderror">
                    @error('deadline') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Upload Files -->
             <div>
                <label for="newFiles" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('app.upload_files') }}
                </label>
                <input type="file" aria-label="{{ __('app.choose_file') }}"
                       id="newFiles"
                       wire:model="newFiles" 
                       multiple 
                       class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt,.xlsx" />
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('app.supported_formats_max_10mb') }}
                </div>
                <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                    {{ __('app.files_auto_upload_notice') }}
                </div>
                @error('newFiles.*') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror

                 @if (session()->has('error'))
                     <div class="mt-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 px-4 py-3 rounded">
                         {{ session('error') }}
                     </div>
                 @endif
                
                <!-- Yükleme Göstergesi -->
                <div wire:loading wire:target="newFiles" class="mt-2">
                    <div class="flex items-center text-blue-600 dark:text-blue-400">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('app.files_uploading') }}
                    </div>
                </div>

                <!-- Kaydedilmiş Dosyalar -->
                @if($savedFiles && count($savedFiles) > 0)
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('app.uploaded_files_count', ['count' => count($savedFiles)]) }}
                        </h4>
                        <div class="space-y-2 max-h-60 overflow-y-auto">
                            @foreach($savedFiles as $file)
                                <div class="flex items-center justify-between bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg px-4 py-3 transition-all duration-200 hover:bg-green-100 dark:hover:bg-green-900/30">
                                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                                        <div class="flex-shrink-0">
                                            @php
                                                $extension = pathinfo($file['original_name'], PATHINFO_EXTENSION);
                                            @endphp
                                            @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png']))
                                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            @elseif(strtolower($extension) === 'pdf')
                                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                {{ $file['original_name'] }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ number_format($file['file_size'] / 1024, 1) }} KB
                                            </p>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            wire:click="removeSavedFile('{{ $file['id'] }}')" 
                                            class="ml-3 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 focus:outline-none transition-colors duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Submit Buttons -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 dark:border-zinc-700">
                <a href="{{ route('admin.tasks') }}" 
                   class="px-6 py-3 border border-gray-300 dark:border-zinc-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-gray-500 transition text-center">
                    {{ __('app.cancel') }}
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition">
                    <span wire:loading.remove>{{ __('app.create_task') }}</span>
                    <span wire:loading>{{ __('app.creating') }}</span>
                </button>
            </div>
        </form>
    </div>
</div> 