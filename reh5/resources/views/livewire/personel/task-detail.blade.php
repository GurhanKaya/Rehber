<div class="w-full max-w-4xl mx-auto mt-6 sm:mt-10" role="main" aria-label="{{ __('app.task_details_aria') }}">
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
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $task->title }}</h1>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if($task->type=='public') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                        @elseif($task->type=='private') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                        @elseif($task->type=='cooperative') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                        @if($task->type == 'cooperative') {{ __('app.cooperative_task') }} 
                        @elseif($task->type == 'public') {{ __('app.open_task') }}
                        @else {{ ucfirst($task->type) }} @endif
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if($task->status=='bekliyor') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                        @elseif($task->status=='devam ediyor') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                        @elseif($task->status=='tamamlandı') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @elseif($task->status=='iptal') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                        @else bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200 @endif">
                        @if($task->status == 'bekliyor') {{ __('app.waiting') }}
                        @elseif($task->status == 'devam ediyor') {{ __('app.in_progress') }}
                        @elseif($task->status == 'tamamlandı') {{ __('app.completed_status') }}
                        @elseif($task->status == 'iptal') {{ __('app.cancelled_status') }}
                        @else {{ ucfirst($task->status) }}
                        @endif
                    </span>
                    @if($isParticipant && !$isAssigned)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                            {{ __('app.participant') }}
                        </span>
                    @endif
                </div>
            </div>
        </header>

        <!-- Task Description -->
        <section class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('app.description') }}</h2>
            <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4 overflow-x-hidden">
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed break-words whitespace-pre-wrap">{{ $task->description }}</p>
            </div>
        </section>

        <!-- Task Info Grid -->
        <section class="space-y-4 mb-6">
            <!-- First Row: Creator, Created Date, Deadline -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                @if($task->creator)
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                        <span class="font-semibold text-purple-800 dark:text-purple-200 block mb-1">{{ __('app.creator') }}</span>
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
                        {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d.m.Y H:i') : __('app.not_specified') }}
                    </span>
                </div>
            </div>

            <!-- Second Row: Assigned Users -->
            @if($task->assignedUser || ($task->participants && $task->participants->count() > 0))
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <span class="font-semibold text-blue-800 dark:text-blue-200 block mb-3">
                        @if($task->type === 'cooperative')
                            {{ __('app.assigned_person_cooperative') }}
                        @else
                            {{ __('app.assigned_person') }}
                        @endif
                    </span>
                    <div class="flex flex-wrap gap-2">
                        @if($task->assignedUser)
                            <div class="flex items-center gap-2 bg-blue-100 dark:bg-blue-800 rounded-lg px-3 py-2">
                                <div class="w-6 h-6 bg-blue-200 dark:bg-blue-700 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-bold text-blue-800 dark:text-blue-200">{{ substr($task->assignedUser->name, 0, 1) }}</span>
                                </div>
                                <span class="text-blue-700 dark:text-blue-200 text-sm font-medium">{{ $task->assignedUser->name }} {{ $task->assignedUser->surname }}</span>
                            </div>
                        @endif
                        @if($task->participants)
                            @foreach($task->participants as $participant)
                                <div class="flex items-center gap-2 bg-blue-100 dark:bg-blue-800 rounded-lg px-3 py-2">
                                    <div class="w-6 h-6 bg-blue-200 dark:bg-blue-700 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-bold text-blue-800 dark:text-blue-200">{{ substr($participant->name, 0, 1) }}</span>
                                    </div>
                                    <span class="text-blue-700 dark:text-blue-200 text-sm font-medium">{{ $participant->name }} {{ $participant->surname }}</span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif
        </section>

        <!-- Files Section -->
        @if($task->files && count($task->files))
            <section class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('app.files') }} ({{ count($task->files) }})</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($task->files as $file)
                        <div class="flex items-center justify-between bg-gray-50 dark:bg-zinc-800 rounded-lg px-4 py-3 border border-gray-200 dark:border-zinc-700">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="bg-blue-100 dark:bg-blue-900 rounded p-2 shrink-0">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('files.download', $file) }}" target="_blank" 
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2 2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </a>
                                @if($file->user_id == auth()->id())
                                    <button type="button" 
                                            wire:click="deleteFile({{ $file->id }})" 
                                            wire:confirm="{{ __('app.confirm_delete_file') }}"
                                            class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs transition"
                                            title="{{ __('app.delete') }}">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Status Update Form -->
        @if($isAssigned || ($task->type === 'public' && !$task->assigned_user_id))
            <section class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('app.update_status_section') }}</h2>
                @if($task->type === 'public' && !$task->assigned_user_id)
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 mb-4">
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            {{ __('app.public_task_update_note') }}
                        </p>
                    </div>
                @endif
                <form wire:submit.prevent="updateStatus" class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    <select wire:model="newStatus" 
                            class="px-4 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="bekliyor">{{ __('app.waiting') }}</option>
                        <option value="devam ediyor">{{ __('app.in_progress') }}</option>
                        <option value="tamamlandı">{{ __('app.completed_status') }}</option>
                        <option value="iptal">{{ __('app.cancelled_status') }}</option>
                    </select>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition">
                        {{ __('app.update_status_button') }}
                    </button>
                    @if($task->type === 'cooperative')
                        <button type="button" 
                                wire:click="leaveTask"
                                wire:confirm="{{ __('app.leave_task_confirm') }}"
                                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            {{ __('app.leave_task') }}
                        </button>
                    @endif
                </form>
            </section>
        @endif

        <!-- File Upload Form -->
        @if($isAssigned || $isParticipant)
            <section class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('app.upload_file') }}</h2>
                <form wire:submit.prevent="uploadFiles" class="space-y-4">
                    <div>
                        <input type="file" aria-label="{{ __('app.choose_file') }}"
                               wire:model="newFiles" 
                               multiple 
                               class="w-full border rounded-lg p-3 dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xlsx,.txt" />
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ __('app.supported_formats_max_10mb') }}
                        </div>
                        @error('uploadedFiles.*') 
                            <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Selected Files List -->
                    @if(count($uploadedFiles) > 0)
                        <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4 border border-gray-200 dark:border-zinc-700">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('personnel.selected_files') }}</h4>
                            <div class="space-y-2">
                                @foreach($uploadedFiles as $index => $file)
                                    <div class="flex items-center justify-between bg-white dark:bg-zinc-900 rounded px-3 py-2 border border-gray-200 dark:border-zinc-600">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                            <span class="text-sm text-gray-700 dark:text-gray-200 truncate">
                                                {{ method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : $file }}
                                            </span>
                                        </div>
                                        <button type="button" 
                                                wire:click="removeUploadedFile({{ $index }})" 
                                                class="text-red-600 hover:text-red-800 ml-2 p-1 focus:outline-none">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-3">
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                @if(count($uploadedFiles) == 0) disabled @endif>
                            {{ __('personnel.upload_files_button') }} @if(count($uploadedFiles) > 0)({{ count($uploadedFiles) }})@endif
                        </button>
                        @if($fileUploadError)
                            <span class="text-red-600 dark:text-red-400 text-sm font-medium">{{ $fileUploadError }}</span>
                        @endif
                    </div>
                </form>
            </section>
        @endif

        <!-- Comments Section -->
        <section class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('app.comments') }}</h2>
            
            @if($isAssigned || $isParticipant)
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
            @endif

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
                                            {{ $comment->user->name ?? 'Kullanıcı' }} {{ $comment->user->surname ?? '' }}
                                        </span>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $comment->created_at->format('d.m.Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                                @if($comment->user_id == auth()->id() || $isAssigned)
                                    <button type="button" 
                                            wire:click="deleteComment({{ $comment->id }})" 
                                            wire:confirm="Bu yorumu silmek istediğinize emin misiniz?"
                                            class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs transition">
                                        Sil
                                    </button>
                                @endif
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

        <!-- Activity Log (Optional) -->
        @if(isset($logs) && $logs->count() > 0)
            <section class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('app.last_activities') }}</h2>
                <div class="max-h-40 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-zinc-600 scrollbar-track-gray-100 dark:scrollbar-track-zinc-800">
                    <div class="space-y-2 pr-2">
                        @foreach($logs->take(5) as $log)
                            <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                <span>{{ $log->user->name ?? 'Kullanıcı' }} - 
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
                                    @elseif($log->action == 'participant_added')
                                        <span class="text-blue-600 dark:text-blue-400">{{ __('app.participant_added') }}</span>
                                    @elseif($log->action == 'task_assigned')
                                        <span class="text-green-600 dark:text-green-400">{{ __('app.task_assigned') }}</span>
                                        @if($log->details)
                                            <span class="text-gray-500 dark:text-gray-400"> - {{ $log->details }}</span>
                                        @endif
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
                                    @if($log->details && $log->action !== 'joined_cooperative_task')
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
                                </span>
                                <span class="text-xs">{{ $log->created_at->format('d.m H:i') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- Footer -->
                <footer class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6 border-t border-gray-200 dark:border-zinc-700">
            <a href="{{ route('personel.tasks') }}" 
               class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-200 dark:bg-zinc-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-zinc-600 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('app.back_to_tasks') }}
            </a>
            
            <!-- Leave Task Button -->
            @if($isAssigned || $isParticipant)
                <button type="button" 
                        wire:click="leaveTask"
                        wire:confirm="{{ __('app.leave_task_confirm') }}"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    {{ __('app.leave_task') }}
                </button>
            @endif
            
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('app.last_update') }}: {{ $task->updated_at->format('d.m.Y H:i') }}
            </div>
        </footer>
    </div>
</div> 