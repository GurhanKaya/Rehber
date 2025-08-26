@php
$statuses = ['' => 'Tümü', 'bekliyor' => 'Bekliyor', 'devam ediyor' => 'Devam Ediyor', 'tamamlandı' => 'Tamamlandı', 'iptal' => 'İptal'];
$types = ['' => 'Tümü', 'public' => 'Açık', 'private' => 'Özel', 'cooperative' => 'İş birliği'];
$deadlineOptions = ['' => 'Tümü', 'today' => 'Bugün', 'this_week' => 'Bu Hafta', 'overdue' => 'Gecikmiş'];
@endphp

<div class="w-full max-w-7xl mx-auto" x-data="{ open: @entangle('showFilters') }" role="main" aria-label="Görev yönetimi">
    
    <!-- Header Section -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Görev Yönetimi</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Tüm görevleri görüntüleyin, düzenleyin ve yönetin</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button 
                    type="button"
                    wire:click="showUnassignedTasks"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white font-semibold rounded-lg shadow-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    Alınmamış Görevler
                </button>
                <a href="{{ route('admin.tasks.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg shadow-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Yeni Görev Ekle
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filters Section -->
    <section class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 sm:p-6 mb-6 space-y-6" role="search" aria-label="Görev arama ve filtreleme">
        <form wire:submit.prevent="searchTasks" class="space-y-4">
            <!-- Search Bar -->
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <label for="task-search" class="sr-only">Görev ara</label>
                    <input
                        id="task-search"
                        type="text"
                        wire:model.defer="search"
                        placeholder="Başlık, açıklama veya atanan kişi ile ara..."
                        class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        aria-describedby="search-help"
                    />
                    <div id="search-help" class="sr-only">Görev başlığı, açıklaması veya atanan kişi adıyla arama yapabilirsiniz</div>
                </div>
                <button
                    type="submit"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition"
                    aria-label="Arama yap"
                >
                    <span class="hidden sm:inline">Ara</span>
                    <svg class="w-5 h-5 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
                @if($hasSearched || $typeFilter || $statusFilter || $assignedFilter || $deadlineFilter)
                    <button
                        type="button"
                        wire:click="clearFilters"
                        class="px-4 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition"
                        aria-label="Filtreleri temizle"
                    >
                        <span class="hidden sm:inline">Temizle</span>
                        <svg class="w-5 h-5 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif
            </div>

            <!-- Filter Toggle -->
            <div class="flex justify-between items-center">
                <button 
                    type="button" 
                    wire:click="toggleFilters" 
                    class="flex items-center space-x-2 text-white bg-blue-600 px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition"
                    aria-expanded="false"
                    :aria-expanded="open"
                    aria-controls="filter-panel"
                    aria-label="Gelişmiş filtreleri göster/gizle"
                >
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    <span>Gelişmiş Filtreler</span>
                </button>
                
                <!-- Task Statistics -->
                <div class="hidden sm:flex space-x-4 text-sm text-gray-600 dark:text-gray-400">
                    <span>Toplam: <strong class="text-gray-900 dark:text-gray-100">{{ $tasks->total() }}</strong></span>
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
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 bg-gray-50 dark:bg-zinc-900 p-4 rounded-lg border border-gray-200 dark:border-zinc-600"
                id="filter-panel"
                role="group"
                aria-label="Gelişmiş filtre seçenekleri"
            >
                <div>
                    <label for="type-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Görev Türü</label>
                    <select 
                        id="type-filter"
                        wire:model="typeFilter" 
                        class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        aria-label="Görev türü filtresi"
                    >
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Durum</label>
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
                    <label for="assigned-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Atanan Kişi</label>
                    <select 
                        id="assigned-filter"
                        wire:model="assignedFilter" 
                        class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        aria-label="Atanan kişi filtresi"
                    >
                        <option value="">Tümü</option>
                        @if(isset($users) && $users->count() > 0)
                            @foreach($users as $user)
                                @if(isset($user->id) && isset($user->name) && isset($user->surname))
                                    <option value="{{ $user->id }}">{{ $user->name }} {{ $user->surname }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label for="deadline-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Son Tarih</label>
                    <select 
                        id="deadline-filter"
                        wire:model="deadlineFilter" 
                        class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        aria-label="Son tarih filtresi"
                    >
                        @foreach($deadlineOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </section>

    <!-- Tasks Grid -->
    <section aria-label="Görev listesi" role="region">
        @if($tasks->count() > 0)
            <div class="sr-only" aria-live="polite">{{ $tasks->count() }} görev bulundu</div>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                @foreach($tasks as $task)
                    <article class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-md p-5 flex flex-col h-full transition hover:shadow-lg focus-within:ring-2 focus-within:ring-blue-500">
                        <header class="flex items-start justify-between mb-3">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 leading-tight">{{ $task->title }}</h3>
                            <span class="ml-2 px-2 py-1 rounded-full text-xs font-semibold shrink-0
                                @if($task->type=='public') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @elseif($task->type=='private') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                @elseif($task->type=='cooperative') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif"
                                role="badge"
                                aria-label="Görev türü: {{ $task->type }}">
                                @if($task->type == 'cooperative') İş birliği @else {{ ucfirst($task->type) }} @endif
                            </span>
                        </header>
                        
                        <div class="mb-3 text-sm text-gray-600 dark:text-gray-300 line-clamp-3 flex-1">
                            {{ $task->description ?: 'Açıklama yok' }}
                        </div>
                        
                        <div class="space-y-2 text-xs text-gray-500 dark:text-gray-400 mt-auto">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span><strong>Atanan:</strong> 
                                    @if($task->assignedUser && is_object($task->assignedUser) && isset($task->assignedUser->name) && isset($task->assignedUser->surname))
                                        {{ $task->assignedUser->name }} {{ $task->assignedUser->surname }}
                                    @else
                                        Atanmamış
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span><strong>Son Tarih:</strong> 
                                    @if($task->deadline)
                                        {{ \Carbon\Carbon::parse($task->deadline)->format('d.m.Y H:i') }}
                                        @if(\Carbon\Carbon::parse($task->deadline)->isPast() && $task->status != 'tamamlandı')
                                            <span class="text-red-500 font-semibold">(Gecikmiş)</span>
                                        @endif
                                    @else
                                        Belirtilmemiş
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-2 h-2 rounded-full mr-2 
                                    @if($task->status == 'tamamlandı') bg-green-500
                                    @elseif($task->status == 'devam ediyor') bg-yellow-500
                                    @elseif($task->status == 'iptal') bg-red-500
                                    @else bg-gray-500 @endif"
                                    aria-hidden="true"></div>
                                <span><strong>Durum:</strong> 
                                    @if($task->status == 'bekliyor') Bekliyor
                                    @elseif($task->status == 'devam ediyor') Devam Ediyor
                                    @elseif($task->status == 'tamamlandı') Tamamlandı
                                    @elseif($task->status == 'iptal') İptal
                                    @else {{ ucfirst($task->status) }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        <footer class="flex gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                            <a href="{{ route('admin.tasks.detail', $task->id) }}" 
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium transition text-center focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center justify-center gap-2"
                               aria-label="{{ $task->title }} görevinin detaylarını görüntüle">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Detayları Görüntüle
                            </a>
                        </footer>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6" role="navigation" aria-label="Sayfalama">
                {{ $tasks->links() }}
            </div>
        @else
            <div class="text-center py-12" role="status">
                <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                    @if($hasSearched || $typeFilter || $statusFilter || $assignedFilter || $deadlineFilter)
                        Arama sonucu bulunamadı
                    @else
                        Henüz görev oluşturulmamış
                    @endif
                </h3>
                <p class="text-gray-500 dark:text-gray-400">
                    @if($hasSearched || $typeFilter || $statusFilter || $assignedFilter || $deadlineFilter)
                        Arama kriterlerinizi değiştirip tekrar deneyin.
                    @else
                        İlk görevinizi oluşturmak için "Yeni Görev Ekle" butonuna tıklayın.
                    @endif
                </p>
            </div>
        @endif
    </section>

    <!-- Task Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $editId ? 'Görev Düzenle' : 'Yeni Görev Ekle' }}
                        </h2>
                        <button wire:click="$set('showModal', false)" 
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 rounded">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form wire:submit.prevent="saveTask" class="space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Görev Başlığı *</label>
                            <input type="text" 
                                   id="title"
                                   wire:model.defer="title" 
                                   class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   placeholder="Görev başlığını girin..." 
                                   required>
                            @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Açıklama</label>
                            <textarea wire:model.defer="description" 
                                      id="description"
                                      rows="4"
                                      class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                      placeholder="Görev açıklaması..."></textarea>
                            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Görev Türü *</label>
                                <select wire:model.defer="type" 
                                        id="type"
                                        class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="public">Açık</option>
                                    <option value="private">Özel</option>
                                    <option value="cooperative">İş birliği</option>
                                </select>
                                @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Durum *</label>
                                <select wire:model.defer="status" 
                                        id="status"
                                        class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="bekliyor">Bekliyor</option>
                                    <option value="devam ediyor">Devam Ediyor</option>
                                    <option value="tamamlandı">Tamamlandı</option>
                                    <option value="iptal">İptal</option>
                                </select>
                                @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{ taskType: @entangle('type') }">
                            <div>
                                <label for="assigned_user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Atanan Personel
                                    <span x-show="taskType === 'private'" class="text-red-500">*</span>
                                </label>
                                <select wire:model.defer="assigned_user_id" 
                                        id="assigned_user_id"
                                        class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="{{ null }}" x-text="taskType === 'private' ? 'Personel seçin (zorunlu)' : 'Personel seçin (isteğe bağlı)'"></option>
                                    @if(isset($users) && $users->count() > 0)
                                        @foreach($users as $user)
                                            @if(isset($user->id) && isset($user->name) && isset($user->surname))
                                                <option value="{{ $user->id }}">{{ $user->name }} {{ $user->surname }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                @error('assigned_user_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                <div x-show="taskType === 'private'" class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                                    Özel görevler için personel ataması zorunludur.
                                </div>
                            </div>

                            <div>
                                <label for="deadline" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Son Tarih</label>
                                <input type="datetime-local" 
                                       id="deadline"
                                       wire:model.defer="deadline" 
                                       class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('deadline') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="files" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dosya Yükle</label>
                            <input type="file" 
                                   id="files"
                                   wire:model="files" 
                                   multiple 
                                   class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt,.xlsx" />
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Desteklenen formatlar: JPG, PNG, PDF, DOC, DOCX, TXT, XLSX. Maksimum dosya boyutu: 10MB
                            </div>
                            @error('uploadedFiles.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            
                            @if(isset($uploadedFiles) && count($uploadedFiles))
                                <ul class="mt-3 space-y-2">
                                    @foreach($uploadedFiles as $index => $file)
                                        <li class="flex items-center justify-between bg-gray-50 dark:bg-zinc-700 rounded px-3 py-2">
                                            <span class="truncate text-sm">{{ method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : $file }}</span>
                                            <button type="button" 
                                                    wire:click="removeFile({{ $index }})" 
                                                    class="text-red-600 hover:text-red-800 ml-2 focus:outline-none">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        @if(isset($existingFiles) && is_array($existingFiles) && count($existingFiles))
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mevcut Dosyalar</label>
                                <ul class="space-y-2">
                                    @foreach($existingFiles as $file)
                                        @if(isset($file['id']) && isset($file['file_name']))
                                            <li class="flex items-center justify-between bg-gray-50 dark:bg-zinc-700 rounded px-3 py-2">
                                                <a href="{{ route('files.download', $file['id']) }}" 
                                                   target="_blank" 
                                                   class="truncate text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                                    {{ $file['file_name'] }}
                                                </a>
                                                <div class="flex items-center gap-2 ml-2">
                                                    <a href="{{ route('files.download', $file['id']) }}" 
                                                       class="text-blue-600 hover:text-blue-800 focus:outline-none"
                                                       title="Dosyayı İndir">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </a>
                                                    <button type="button" 
                                                            wire:click="removeExistingFile({{ $file['id'] }})" 
                                                            class="text-red-600 hover:text-red-800 focus:outline-none"
                                                            onclick="return confirm('Bu dosyayı silmek istediğinize emin misiniz?')"
                                                            title="Dosyayı Sil">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 dark:border-zinc-700">
                            <button type="button" 
                                    wire:click="$set('showModal', false)" 
                                    class="px-6 py-3 border border-gray-300 dark:border-zinc-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-gray-500 transition">
                                İptal
                            </button>
                            @if($editId)
                                <button type="button" 
                                        wire:click="deleteTask({{ $editId }})" 
                                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition"
                                        onclick="return confirm('Bu görevi silmek istediğinize emin misiniz?')">
                                    Görevi Sil
                                </button>
                            @endif
                            <button type="submit" 
                                    class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition">
                                {{ $editId ? 'Güncelle' : 'Oluştur' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>