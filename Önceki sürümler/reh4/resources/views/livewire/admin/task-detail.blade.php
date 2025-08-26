<div class="w-full max-w-4xl mx-auto mt-6 sm:mt-10" role="main" aria-label="{{ __('app.task_details_aria_admin') }}">
    <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-2xl shadow-xl p-6 sm:p-8">
        
        <!-- Task Header -->
        <header class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-6">
            <div class="bg-blue-100 dark:bg-blue-900 rounded-full p-3 shrink-0">
                <svg class="w-8 h-8 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($task->type == 'cooperative')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    @endif
                </svg>
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-4 mb-2">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 flex-1">{{ $task->title }}</h1>
                    <button 
                        type="button" 
                        wire:click="toggleEditMode" 
                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 flex items-center gap-2 font-medium">
                        @if($editMode)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            {{ __('app.view') }}
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            {{ __('app.edit') }}
                        @endif
                    </button>
                </div>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if($task->type=='public') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                        @elseif($task->type=='private') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                        @elseif($task->type=='cooperative') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                        @if($task->type == 'public') {{ __('app.open_task') }}
                        @elseif($task->type == 'private') {{ __('app.private_task') }}
                        @elseif($task->type == 'cooperative') {{ __('app.cooperative_task') }}
                        @else {{ ucfirst($task->type) }}
                        @endif
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if($task->status=='bekliyor') bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200
                        @elseif($task->status=='devam ediyor') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                        @elseif($task->status=='tamamlandı') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                        @if($task->status == 'bekliyor') {{ __('app.waiting') }}
                        @elseif($task->status == 'devam ediyor') {{ __('app.in_progress') }}  
                        @elseif($task->status == 'tamamlandı') {{ __('app.completed_status') }}
                        @elseif($task->status == 'iptal') {{ __('app.cancelled_status') }}
                        @else {{ ucfirst($task->status) }}
                        @endif
                    </span>
                </div>
            </div>
        </header>

        @if($editMode)
            <!-- Edit Form -->
            <form wire:submit.prevent="updateTask" class="space-y-6 mb-8">
                
                <!-- Title Input -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <span class="text-red-500">*</span> {{ __('app.task_title') }}
                    </label>
                    <input type="text" 
                           id="title"
                           wire:model.defer="title" 
                           class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror"
                           placeholder="{{ __('app.enter_task_title') }}">
                    @error('title') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.description') }}</label>
                    <textarea wire:model.defer="description" 
                              id="description"
                              rows="4"
                              class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror" 
                              placeholder="{{ __('app.enter_task_description') }}"></textarea>
                    @error('description') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Type, Status and Deadline -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <span class="text-red-500">*</span> {{ __('app.task_type') }}
                        </label>
                        <select wire:model.defer="type" 
                                id="type"
                                class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror">
                            <option value="public">{{ __('app.open_task') }}</option>
                            <option value="private">{{ __('app.private_task') }}</option>
                            <option value="cooperative">{{ __('app.cooperative_task') }}</option>
                        </select>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            @if($type == 'public') {{ __('app.open_task_desc') }}
                            @elseif($type == 'private') {{ __('app.private_task_desc') }}
                            @else {{ __('app.cooperative_task_desc') }}
                            @endif
                        </div>
                        @error('type') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <span class="text-red-500">*</span> {{ __('app.status') }}
                        </label>
                        <select wire:model.defer="status" 
                                id="status"
                                class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                            <option value="bekliyor">{{ __('app.waiting') }}</option>
                            <option value="devam ediyor">{{ __('app.in_progress') }}</option>
                            <option value="tamamlandı">{{ __('app.completed_status') }}</option>
                            <option value="iptal">{{ __('app.cancelled_status') }}</option>
                        </select>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            @if($status == 'bekliyor') {{ __('app.not_started_yet') }}
                            @elseif($status == 'devam ediyor') {{ __('app.actively_working') }}
                            @elseif($status == 'tamamlandı') {{ __('app.task_completed') }}
                            @else {{ __('app.cancelled') }}
                            @endif
                        </div>
                        @error('status') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="deadline" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('app.deadline') }}</label>
                        <input type="datetime-local" 
                               id="deadline"
                               wire:model.defer="deadline" 
                               class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('deadline') border-red-500 @enderror"
                               placeholder="{{ __('app.select_deadline') }}">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            @if($deadline)
                                {{ __('app.selected_date') }}: {{ \Carbon\Carbon::parse($deadline)->format('d.m.Y H:i') }}
                            @else
                                {{ __('app.no_deadline_specified') }}
                            @endif
                        </div>
                        @error('deadline') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                 <!-- Assignees -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        <span class="text-red-500">*</span> {{ __('app.assignees') }}
                    </label>
                    
                    <!-- Mevcut Atanmış Kişiler -->
                    @if($assignedUsers && is_array($assignedUsers) && count($assignedUsers) > 0)
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach($assignedUsers as $user)
                                    @if(is_object($user) && isset($user->id) && isset($user->name) && isset($user->surname))
                                        <span class="inline-flex items-center gap-2 px-3 py-2 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-lg text-sm font-medium shadow-sm">
                                            <div class="w-6 h-6 bg-green-200 dark:bg-green-800 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-bold">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-1">
                                                    <span class="font-medium">{{ $user->name }} {{ $user->surname }}</span>
                                                    @if(isset($user->role) && $user->role === 'admin')
                                                        <span class="px-1.5 py-0.5 bg-purple-200 text-purple-800 dark:bg-purple-800 dark:text-purple-200 text-xs rounded font-medium">Admin</span>
                                                    @endif
                                                </div>
                                                @if(isset($user->title))
                                                    <span class="text-xs opacity-75">{{ $user->title }}</span>
                                                @endif
                                            </div>
                                            <button type="button" 
                                                    wire:click="removeAssignedUser({{ $user->id }})" 
                                                    class="ml-1 text-red-600 hover:text-red-800 dark:text-red-300 dark:hover:text-red-100 transition-colors"
                                                    title="{{ __('app.remove_from_assignees') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <span class="text-yellow-800 dark:text-yellow-200 text-sm">{{ __('app.no_assigned_person_yet') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Personel Arama -->
                    <div class="relative">
                        <div class="relative">
                            <input type="text" 
                                   wire:model.live="searchQuery"
                                   wire:keydown.enter.prevent="selectFirstUser"
                                   placeholder="{{ __('app.search_staff_admin') }}"
                                   class="w-full px-4 py-3 pl-10 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <div wire:loading class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Arama Sonuçları -->
                        @if($searchQuery && strlen($searchQuery) >= 2)
                            <div class="absolute z-10 w-full mt-1 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <div wire:loading class="p-4 text-center">
                                    <div class="flex items-center justify-center">
                                        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('app.searching') }}</span>
                                    </div>
                                </div>
                                <div wire:loading.remove>
                                @if($searchResults && $searchResults->count() > 0)
                                    <div class="p-2 text-xs text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-zinc-700">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                            {{ __('app.press_enter_select_first') }}
                                        </span>
                                    </div>
                                    @foreach($searchResults as $user)
                                        @if(isset($user->id) && isset($user->name) && isset($user->surname))
                                            <button type="button" 
                                                    wire:click="assignUser({{ $user->id }})"
                                                    wire:keydown.enter="assignUser({{ $user->id }})"
                                                    class="w-full px-4 py-3 text-left hover:bg-gray-50 dark:hover:bg-zinc-700 flex items-center gap-3 border-b last:border-b-0 border-gray-100 dark:border-zinc-700 transition-colors">
                                                <div class="w-8 h-8 bg-gray-200 dark:bg-zinc-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <span class="text-sm font-bold text-gray-600 dark:text-gray-300">{{ substr($user->name, 0, 1) }}</span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                                        {{ $user->name }} {{ $user->surname }}
                                                        @if(isset($user->role) && $user->role === 'admin')
                                                            <span class="px-2 py-0.5 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 text-xs rounded-full font-medium">Admin</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                                        @if(isset($user->title) && isset($user->department))
                                                            {{ $user->title }} - {{ $user->department }}
                                                        @elseif(isset($user->title))
                                                            {{ $user->title }}
                                                        @elseif(isset($user->department))
                                                            {{ $user->department }}
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                <span class="text-xs text-green-600 dark:text-green-400 font-medium">{{ __('app.add') }}</span>
                                                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                </div>
                                            </button>
                                        @endif
                                    @endforeach
                                @else
                                    <div class="p-4 text-center">
                                        <div class="text-gray-500 dark:text-gray-400">
                                            <svg class="mx-auto h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                            <p class="font-medium mb-1">{{ __('app.no_staff_found') }}</p>
                                            <p class="text-sm">{{ __('app.try_different_keywords') }}</p>
                                        </div>
                                    </div>
                                @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('app.staff_search_help') }}
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3 pt-6 border-t border-gray-200 dark:border-zinc-700">
                    <!-- Delete Button -->
                    <button type="button" 
                            wire:click="deleteTask"
                            wire:confirm="{{ __('app.confirm_delete_task_irreversible') }}"
                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        {{ __('app.delete_task') }}
                    </button>
                    
                    <!-- Save/Cancel Buttons -->
                    <div class="flex gap-3">
                        <button type="button" 
                                wire:click="$set('editMode', false)" 
                                class="px-6 py-3 border border-gray-300 dark:border-zinc-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-gray-500 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            {{ __('app.cancel') }}
                        </button>
                        <button type="submit" 
                                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span wire:loading.remove>{{ __('app.save_changes') }}</span>
                            <span wire:loading>{{ __('app.saving') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        @else
            <!-- Display Mode -->
            
            <!-- Task Description -->
            <section class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('app.description') }}</h2>
                <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4 overflow-x-hidden">
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed break-words whitespace-pre-wrap">{{ $task->description ?: __('app.no_description_available') }}</p>
                </div>
            </section>

            <!-- Task Info Grid -->
            <section class="space-y-4 mb-6">
                <!-- First Row: Creator, Created Date, Deadline -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    @if($task->creator && is_object($task->creator) && isset($task->creator->name) && isset($task->creator->surname))
                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                            <span class="font-semibold text-purple-800 dark:text-purple-200 block mb-1">{{ __('app.created_by') }}</span>
                            <span class="text-purple-600 dark:text-purple-300">{{ $task->creator->name }} {{ $task->creator->surname }}</span>
                        </div>
                    @endif

                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <span class="font-semibold text-green-800 dark:text-green-200 block mb-1">{{ __('app.created') }}</span>
                        <span class="text-green-600 dark:text-green-300">{{ $task->created_at->format('d.m.Y H:i') }}</span>
                    </div>

                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                        <span class="font-semibold text-orange-800 dark:text-orange-200 block mb-1">{{ __('app.deadline') }}</span>
                        <span class="text-orange-600 dark:text-orange-300">
                            @if($task->deadline)
                                {{ \Carbon\Carbon::parse($task->deadline)->format('d.m.Y H:i') }}
                                @if(\Carbon\Carbon::parse($task->deadline)->isPast() && $task->status != 'tamamlandı')
                                    <span class="text-red-500 font-semibold">({{ __('app.overdue') }})</span>
                                @endif
                            @else
                                {{ __('app.not_specified') }}
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Second Row: Assigned Users -->
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <span class="font-semibold text-green-800 dark:text-green-200 block mb-3">
                        @if($task->type === 'cooperative')
                            {{ __('app.assigned_person_cooperative') }}
                        @else
                            {{ __('app.assignees') }}
                        @endif
                    </span>
                    @if(($task->assignedUser && is_object($task->assignedUser)) || ($task->participants && $task->participants->count() > 0))
                        <div class="flex flex-wrap gap-2">
                            @if($task->assignedUser && is_object($task->assignedUser) && isset($task->assignedUser->name) && isset($task->assignedUser->surname))
                                <div class="flex items-center gap-2 bg-green-100 dark:bg-green-800 rounded-lg px-3 py-2">
                                    <div class="w-6 h-6 bg-green-200 dark:bg-green-700 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-bold text-green-800 dark:text-green-200">{{ substr($task->assignedUser->name, 0, 1) }}</span>
                                    </div>
                                    <span class="text-green-700 dark:text-green-200 text-sm font-medium">{{ $task->assignedUser->name }} {{ $task->assignedUser->surname }}</span>
                                    @if(isset($task->assignedUser->role) && $task->assignedUser->role === 'admin')
                                        <span class="px-2 py-0.5 bg-purple-200 text-purple-800 dark:bg-purple-800 dark:text-purple-200 text-xs rounded font-medium">Admin</span>
                                    @endif
                                </div>
                            @endif
                            @if($task->participants)
                                @foreach($task->participants as $participant)
                                    @if(is_object($participant) && isset($participant->name) && isset($participant->surname))
                                        <div class="flex items-center gap-2 bg-green-100 dark:bg-green-800 rounded-lg px-3 py-2">
                                            <div class="w-6 h-6 bg-green-200 dark:bg-green-700 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-bold text-green-800 dark:text-green-200">{{ substr($participant->name, 0, 1) }}</span>
                                            </div>
                                            <span class="text-green-700 dark:text-green-200 text-sm font-medium">{{ $participant->name }} {{ $participant->surname }}</span>
                                            @if(isset($participant->role) && $participant->role === 'admin')
                                                <span class="px-2 py-0.5 bg-purple-200 text-purple-800 dark:bg-purple-800 dark:text-purple-200 text-xs rounded font-medium">Admin</span>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    @else
                        <span class="text-green-600 dark:text-green-300 text-sm">{{ __('app.unassigned') }}</span>
                    @endif
                </div>
            </section>
        @endif

        <!-- Files Section -->
        @if($task->files && count($task->files))
            <section class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('app.files') }} ({{ count($task->files) }})</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($task->files as $file)
                        <div class="flex items-center justify-between bg-gray-50 dark:bg-zinc-800 rounded-lg px-4 py-3 border border-gray-200 dark:border-zinc-700">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="bg-blue-100 dark:bg-blue-900 rounded p-2 shrink-0">
                                    @php
                                        $extension = pathinfo($file->file_name, PATHINFO_EXTENSION);
                                    @endphp
                                    @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png']))
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    @elseif(strtolower($extension) === 'pdf')
                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('files.view', $file) }}" target="_blank" 
                                       class="text-blue-600 hover:underline dark:text-blue-400 font-medium block truncate"
                                       title="{{ $file->file_name }}">
                                        {{ $file->file_name }}
                                    </a>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ number_format($file->file_size / 1024, 1) }} KB
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 ml-3 shrink-0">
                                <a href="{{ route('files.download', $file) }}" 
                                   class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs transition"
                                   title="{{ __('app.download') }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </a>
                                <button type="button" 
                                        wire:click="deleteFile({{ $file->id }})" 
                                        wire:confirm="{{ __('app.confirm_delete_file') }}"
                                        class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs transition"
                                        title="{{ __('app.delete') }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

                 <!-- File Upload Form -->
        <section class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('app.upload_file') }}</h2>
             <div class="space-y-4">
                 <div>
                     <input type="file" aria-label="{{ __('app.choose_file') }}"
                            wire:model="newFiles" 
                            multiple 
                            class="w-full border rounded-lg p-3 dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xlsx,.txt" />
                      <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                          {{ __('app.files_auto_upload_notice') }} {{ __('app.supported_formats_max_10mb') }}
                      </div>
                     @error('newFiles.*') 
                         <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> 
                     @enderror
                 </div>

                 <!-- Yükleme Göstergesi -->
                 <div wire:loading wire:target="newFiles" class="flex items-center text-blue-600 dark:text-blue-400">
                     <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                         <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                         <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                     </svg>
                     {{ __('app.files_uploading') }}
                 </div>

                 <!-- Status Messages -->
                 <div class="flex items-center gap-3">
                     @if(session('error'))
                         <span class="text-red-600 dark:text-red-400 text-sm font-medium flex items-center">
                             <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                             </svg>
                             {{ session('error') }}
                         </span>
                     @endif
                     @if(session('success'))
                         <span class="text-green-600 dark:text-green-400 text-sm font-medium flex items-center">
                             <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                             </svg>
                             {{ session('success') }}
                         </span>
                     @endif
                 </div>
             </div>
         </section>



        <!-- Comments Section -->
        <section class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('app.comments') }}</h2>
            
            <form wire:submit.prevent="addComment" class="mb-6">
                <div class="flex gap-3">
                    <input type="text" 
                           wire:model.defer="newComment" 
                           placeholder="{{ __('app.add_comment') }}" 
                           class="flex-1 px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           maxlength="1000">
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                        {{ __('app.add') }}
                    </button>
                </div>
                @error('newComment') 
                    <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> 
                @enderror
            </form>

            <div class="max-h-64 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-zinc-600 scrollbar-track-gray-100 dark:scrollbar-track-zinc-800">
                <div class="space-y-4 pr-2">
                    @forelse($comments as $comment)
                        <article class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4 border border-gray-200 dark:border-zinc-700">
                            <header class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-semibold text-blue-600 dark:text-blue-300">
                                            {{ substr($comment->user->name ?? 'K', 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $comment->user->name ?? __('app.user') }} {{ $comment->user->surname ?? '' }}
                                        </span>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $comment->created_at->format('d.m.Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                                <button type="button" 
                                        wire:click="deleteComment({{ $comment->id }})" 
                                        wire:confirm="{{ __('app.confirm_delete_comment') }}"
                                        class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs transition">
                                    {{ __('app.delete') }}
                                </button>
                            </header>
                            <div class="text-gray-700 dark:text-gray-300 break-words whitespace-pre-wrap">{{ $comment->comment }}</div>
                        </article>
                    @empty
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p>{{ __('app.no_comments_yet') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- Activity Log -->
        @if(isset($logs) && $logs->count() > 0)
            <section class="mb-6">
                <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('app.last_activities') }}</h2>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.showing_last_10_activities') }}</span>
                </div>
                <div class="max-h-64 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-zinc-600 scrollbar-track-gray-100 dark:scrollbar-track-zinc-800">
                    <div class="space-y-3 pr-2">
                        @foreach($logs as $log)
                            <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4 flex items-start gap-3 border border-gray-200 dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-semibold text-blue-600 dark:text-blue-300">
                                        {{ substr($log->user->name ?? 'K', 0, 1) }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $log->user->name ?? __('app.user') }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $log->created_at->format('d.m.Y H:i') }}</span>
                                    </div>
                                    <div class="text-gray-700 dark:text-gray-200 text-sm">
                                        @if($log->action == 'task_updated')
                                            <span class="text-blue-600 dark:text-blue-400">{{ __('app.task_updated') }}</span>
                                        @elseif($log->action == 'status_updated')
                                            <span class="text-yellow-600 dark:text-yellow-400">{{ __('app.status_updated') }}</span>
                                        @elseif($log->action == 'files_uploaded')
                                            <span class="text-green-600 dark:text-green-400">{{ __('app.files_uploaded') }}</span>
                                        @elseif($log->action == 'file_deleted')
                                            <span class="text-red-600 dark:text-red-400">{{ __('app.file_deleted') }}</span>
                                        @elseif($log->action == 'comment_added')
                                            <span class="text-purple-600 dark:text-purple-400">{{ __('app.comment_added') }}</span>
                                        @elseif($log->action == 'comment_deleted')
                                            <span class="text-red-600 dark:text-red-400">{{ __('app.comment_deleted') }}</span>
                                        @elseif($log->action == 'user_assigned')
                                            <span class="text-green-600 dark:text-green-400">{{ __('app.user_assigned') }}</span>
                                        @elseif($log->action == 'user_removed')
                                            <span class="text-red-600 dark:text-red-400">{{ __('app.user_removed') }}</span>
                                        @elseif($log->action == 'task_created')
                                            <span class="text-green-600 dark:text-green-400">{{ __('app.task_created') }}</span>
                                        @elseif($log->action == 'deadline_changed')
                                            <span class="text-orange-600 dark:text-orange-400">{{ __('app.deadline_changed') }}</span>
                                        @elseif($log->action == 'task_type_changed')
                                            <span class="text-purple-600 dark:text-purple-400">{{ __('app.task_type_changed') }}</span>
                                        @elseif($log->action == 'joined_cooperative_task')
                                            <span class="text-green-600 dark:text-green-400">{{ __('app.joined_cooperative_task') }}</span>
                                        @elseif($log->action == 'user_left_task')
                                            <span class="text-red-600 dark:text-red-400">{{ __('app.user_left_task') }}</span>
                                        @else
                                            <span class="text-gray-600 dark:text-gray-400">{{ ucfirst($log->action) }}</span>
                                        @endif
                                        @if($log->details)
                                            <span class="text-gray-500 dark:text-gray-400"> - 
                                                @if($log->action == 'status_updated' || $log->action == 'task_type_changed')
                                                    @php
                                                        $parts = explode(' → ', $log->details);
                                                        if (count($parts) == 2) {
                                                            $oldValue = $parts[0];
                                                            $newValue = $parts[1];
                                                            
                                                            if ($log->action == 'status_updated') {
                                                                $oldValue = __('app.status_' . $oldValue);
                                                                $newValue = __('app.status_' . $newValue);
                                                            } elseif ($log->action == 'task_type_changed') {
                                                                $oldValue = __('app.task_type_' . $oldValue);
                                                                $newValue = __('app.task_type_' . $newValue);
                                                            }
                                                            
                                                            echo $oldValue . ' → ' . $newValue;
                                                        } else {
                                                            echo $log->details;
                                                        }
                                                    @endphp
                                                @else
                                                    @if(str_contains($log->details, 'not_specified'))
                                                        {{ str_replace('not_specified', __('app.not_specified'), $log->details) }}
                                                    @else
                                                        {{ $log->details }}
                                                    @endif
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- Footer -->
        <footer class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6 border-t border-gray-200 dark:border-zinc-700">
            <a href="{{ route('admin.tasks') }}" 
               class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-200 dark:bg-zinc-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-zinc-600 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('app.back_to_tasks') }}
            </a>
            
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('app.last_update') }}: {{ $task->updated_at->format('d.m.Y H:i') }}
            </div>
        </footer>
    </div>
</div> 