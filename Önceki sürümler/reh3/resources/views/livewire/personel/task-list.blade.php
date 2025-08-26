@php 
$statuses = ['' => 'Tümü', 'bekliyor' => 'Bekliyor', 'devam ediyor' => 'Devam Ediyor', 'tamamlandı' => 'Tamamlandı', 'iptal' => 'İptal'];
$types = ['' => 'Tümü', 'public' => 'Açık', 'private' => 'Özel', 'cooperative' => 'İş birliği'];
$priorities = ['' => 'Tümü', 'low' => 'Düşük', 'medium' => 'Orta', 'high' => 'Yüksek', 'urgent' => 'Acil'];
@endphp

<div class="w-full max-w-6xl mx-auto mt-4 sm:mt-8" role="main" aria-label="Görev listesi" x-data="{ open: @entangle('showFilters') }">
    <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg shadow p-4 sm:p-6">
        <!-- Header with Açık Görevler Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <header>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Görevlerim</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Size atanmış görevleri görüntüleyin ve yönetin</p>
            </header>
            <a href="{{ route('personel.public-tasks') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition font-semibold">
                Açık Görevler
            </a>
        </div>
        
        <!-- Filtreler Alanı -->
        <section class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 sm:p-6 mb-6 space-y-6" role="search" aria-label="Görev arama ve filtreleme">
            <form wire:submit.prevent="search" class="space-y-4">
                <!-- Arama Alanı ve Butonlar -->
                <div class="flex flex-col lg:flex-row gap-4">
                    <div class="flex-1">
                        <label for="task-search" class="sr-only">Görev ara</label>
                        <input
                            id="task-search"
                            type="text"
                            wire:model.debounce.500ms="query"
                            placeholder="Başlık veya açıklama ile ara..."
                            class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            aria-describedby="search-help"
                        />
                        <div id="search-help" class="sr-only">Görev başlığı veya açıklamasında arama yapabilirsiniz</div>
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
                    @if($searched || $status || $type || $onlyDeadlineToday || $onlyMyTasks)
                        <button
                            type="button"
                            wire:click="clearSearch"
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

                <!-- Quick Filter Buttons -->
                <div class="flex flex-wrap gap-2 items-center">
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
                        <span>Filtreler</span>
                    </button>
                    
                    <button 
                        type="button" 
                        wire:click="filterDeadlineToday" 
                        class="px-4 py-2 rounded-lg font-semibold transition @if($onlyDeadlineToday) bg-orange-600 text-white @else bg-gray-200 dark:bg-zinc-700 dark:text-white @endif"
                    >
                        Bugün Bitenler
                    </button>
                    
                    <button 
                        type="button" 
                        wire:click="filterMyTasks" 
                        class="px-4 py-2 rounded-lg font-semibold transition @if($onlyMyTasks) bg-purple-600 text-white @else bg-gray-200 dark:bg-zinc-700 dark:text-white @endif"
                    >
                        Sadece Benim
                    </button>
                </div>

                <!-- Detaylı Filtre Alanları -->
                <div 
                    x-show="open" 
                    x-transition 
                    class="grid grid-cols-1 md:grid-cols-3 gap-4"
                    id="filter-panel"
                    role="group"
                    aria-label="Gelişmiş filtre seçenekleri"
                >
                    <div>
                        <label for="status-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Durum</label>
                        <select 
                            id="status-filter"
                            wire:model="status" 
                            class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            aria-label="Durum filtresi"
                        >
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="type-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tür</label>
                        <select 
                            id="type-filter"
                            wire:model="type" 
                            class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            aria-label="Tür filtresi"
                        >
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>


                </div>
            </form>
        </section>

        <section aria-label="Görev kartları" role="region">
            @if($tasks->count() > 0)
                <div class="sr-only" aria-live="polite">{{ $tasks->count() }} görev bulundu</div>
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4 lg:gap-6">
                    @foreach($tasks as $task)
                        <article class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-md p-4 sm:p-5 flex flex-col h-full transition hover:shadow-lg focus-within:ring-2 focus-within:ring-blue-500">
                            <header class="flex items-start justify-between mb-3">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 leading-tight">{{ $task->title }}</h3>
                                <span class="ml-2 px-2 py-1 rounded text-xs font-semibold shrink-0
                                    @if($task->type=='public') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($task->type=='private') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                    @elseif($task->type=='cooperative') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif"
                                    role="badge"
                                    aria-label="Görev türü: {{ ucfirst($task->type) }}">
                                    @if($task->type == 'cooperative') İş birliği @else {{ ucfirst($task->type) }} @endif
                                </span>
                            </header>
                            
                            <div class="mb-3 text-sm text-gray-600 dark:text-gray-300 line-clamp-3 flex-1">
                                {{ $task->description }}
                            </div>
                            
                            <!-- Task Creator/Assigner Info -->
                            @if($task->type == 'cooperative' && $task->creator)
                                <div class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="font-medium">Oluşturan:</span> {{ $task->creator->name }} {{ $task->creator->surname }}
                                </div>
                            @endif

                            <!-- Participants Info for Cooperative Tasks -->
                            @if($task->type == 'cooperative' && $task->participants->count() > 0)
                                <div class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="font-medium">Katılımcılar:</span> 
                                    {{ $task->participants->pluck('name')->join(', ') }}
                                </div>
                            @endif
                            
                            <div class="space-y-2 text-xs text-gray-500 dark:text-gray-400 mt-auto">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span><strong>Deadline:</strong> {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d.m.Y H:i') : 'Belirtilmemiş' }}</span>
                                </div>
                                @if($task->creator)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span><strong>Oluşturan:</strong> {{ $task->creator->name }} {{ $task->creator->surname }}</span>
                                    </div>
                                @endif
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
                            
                            <footer class="mt-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                                <a 
                                    href="{{ route('personel.tasks.detail', $task->id) }}" 
                                    class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition text-center font-medium"
                                    aria-label="{{ $task->title }} görevinin detaylarını görüntüle"
                                >
                                    Detayları Görüntüle
                                </a>
                            </footer>
                        </article>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($tasks->hasPages())
                    <div class="mt-6 flex justify-center">
                        {{ $tasks->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12" role="status">
                    <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                        @if($searched || $status || $type || $onlyDeadlineToday || $onlyMyTasks)
                            Arama sonucu bulunamadı
                        @else
                            Henüz görev bulunmuyor
                        @endif
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        @if($searched || $status || $type || $onlyDeadlineToday || $onlyMyTasks)
                            Arama kriterlerinizi değiştirip tekrar deneyin.
                        @else
                            Size atanmış görev bulunmuyor.
                        @endif
                    </p>
                </div>
            @endif
        </section>
    </div>
</div> 