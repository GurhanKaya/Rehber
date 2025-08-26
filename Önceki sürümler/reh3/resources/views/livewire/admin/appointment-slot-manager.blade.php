@php
$days = ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi'];
@endphp

<div class="w-full max-w-6xl mx-auto" x-data="{ open: @entangle('showFilters') }" role="main" aria-label="Randevu saatleri yönetimi">
    
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
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Randevu Saatleri Yönetimi</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Personel randevu saatlerini görüntüleyin ve düzenleyin</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.appointments') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Randevulara Dön
                </a>
                <button wire:click="showCreateModal" 
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg shadow-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Yeni Saat Ekle
                </button>
            </div>
        </div>
    </div>

    <!-- Search and Filters Section -->
    <section class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 sm:p-6 mb-6 space-y-6" role="search" aria-label="Randevu saati arama ve filtreleme">
        <form wire:submit.prevent="searchSlots" class="space-y-4">
            <!-- Search Bar -->
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <label for="slot-search" class="sr-only">Randevu saati ara</label>
                    <input
                        id="slot-search"
                        type="text"
                        wire:model.defer="search"
                        placeholder="Personel adı ile ara..."
                        class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        aria-describedby="search-help"
                    />
                    <div id="search-help" class="sr-only">Personel adı ile arama yapabilirsiniz</div>
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
                @if($hasSearched || $selectedUser || $selectedDay !== '' || $selectedStatus !== '')
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
                
                <!-- Slot Statistics -->
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
                class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 dark:bg-zinc-900 p-4 rounded-lg border border-gray-200 dark:border-zinc-600"
                id="filter-panel"
                role="group"
                aria-label="Gelişmiş filtre seçenekleri"
            >
                <div>
                    <label for="user-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Personel</label>
                    <select 
                        id="user-filter"
                        wire:model="selectedUser" 
                        class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        aria-label="Personel filtresi"
                    >
                        <option value="">Tümü</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} {{ $user->surname }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="day-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gün</label>
                    <select 
                        id="day-filter"
                        wire:model="selectedDay" 
                        class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        aria-label="Gün filtresi"
                    >
                        <option value="">Tümü</option>
                        @foreach($days as $key => $day)
                            <option value="{{ $key }}">{{ $day }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Durum</label>
                    <select 
                        id="status-filter"
                        wire:model="selectedStatus" 
                        class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        aria-label="Durum filtresi"
                    >
                        <option value="">Tümü</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Pasif</option>
                    </select>
                </div>
            </div>
        </form>
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
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $slot->is_available ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                            {{ $slot->is_available ? 'Aktif' : 'Pasif' }}
                        </span>
                    </div>
                    
                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span><strong>Saat:</strong> {{ $slot->start_time }} - {{ $slot->end_time }}</span>
                        </div>
                    </div>
                    
                    <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                        <button wire:click="showEditModal({{ $slot->id }})" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium transition text-sm">
                            Düzenle
                        </button>
                        <button wire:click="deleteSlot({{ $slot->id }})" 
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                                onclick="return confirm('Bu randevu saatini silmek istediğinize emin misiniz?')">
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
                    Arama kriterlerinize uygun randevu saati bulunamadı
                @else
                    Henüz randevu saati eklenmemiş
                @endif
            </h3>
            <p class="text-gray-500 dark:text-gray-400">
                @if($hasSearched)
                    Farklı arama kriterleri deneyin veya yeni randevu saati ekleyin.
                @else
                    İlk randevu saatini eklemek için "Yeni Saat Ekle" butonuna tıklayın.
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
                        {{ $editId ? 'Randevu Saati Düzenle' : 'Yeni Randevu Saati' }}
                    </h2>
                    
                    <form wire:submit.prevent="saveSlot" class="space-y-4">
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Personel *</label>
                            <select wire:model="user_id" id="user_id" class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white" required>
                                <option value="">Personel seçin</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} {{ $user->surname }}</option>
                                @endforeach
                            </select>
                            @error('user_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Mevcut Randevu Saatleri -->
                        @if($user_id && $existingSlotsForUser->count() > 0)
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Bu kişinin mevcut randevu saatleri:</h4>
                                <div class="space-y-1">
                                    @foreach($existingSlotsForUser as $slot)
                                        <div class="text-xs text-blue-700 dark:text-blue-300">
                                            <span class="font-medium">{{ $days[$slot->day_of_week] }}:</span> 
                                            {{ $slot->start_time }} - {{ $slot->end_time }}
                                            @if(!$slot->is_available)
                                                <span class="text-red-600 dark:text-red-400">(Pasif)</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div>
                            <label for="day_of_week" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gün *</label>
                            <select wire:model.defer="day_of_week" id="day_of_week" class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white" required>
                                <option value="">Gün seçin</option>
                                @foreach($days as $index => $day)
                                    <option value="{{ $index }}">{{ $day }}</option>
                                @endforeach
                            </select>
                            @error('day_of_week') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Başlangıç *</label>
                                <input type="time" wire:model.defer="start_time" id="start_time" class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white" required>
                                @error('start_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bitiş *</label>
                                <input type="time" wire:model.defer="end_time" id="end_time" class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-800 dark:border-zinc-600 dark:text-white" required>
                                @error('end_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model.defer="is_available" class="rounded border-gray-300 text-blue-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                            </label>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 border rounded-lg text-gray-700 dark:text-gray-300">İptal</button>
                            @if($editId)
                                <button type="button" wire:click="deleteSlot({{ $editId }})" class="px-4 py-2 bg-red-600 text-white rounded-lg" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</button>
                            @endif
                            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg">{{ $editId ? 'Güncelle' : 'Oluştur' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
