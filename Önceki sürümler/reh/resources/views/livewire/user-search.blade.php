<div class="w-full max-w-5xl mx-auto" x-data="{ open: @entangle('showFilters'), view: @entangle('viewMode') }">
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6 mb-6 space-y-6">
        <form wire:submit.prevent="search" class="space-y-4">
            <div class="flex flex-col md:flex-row gap-4">
                <input type="text" wire:model.debounce.500ms="query" placeholder="İsim, soyad, unvan..."
                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg dark:bg-zinc-900 dark:border-zinc-600 dark:text-white" />
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Ara
                </button>
            </div>

            <div class="flex justify-between items-center">
                <button type="button" wire:click="toggleFilters"
                    class="flex items-center space-x-2 bg-blue-600 text-white px-4 py-2 rounded-lg">
                    <svg class="w-6 h-6 transition-transform duration-300 transform"
                        :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                    <span>Filtreler</span>
                </button>

                <div class="flex space-x-2">
                    <button type="button" x-on:click="view = 'grid'" class="p-2 rounded-lg"
                        :class="view === 'grid' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-zinc-700'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 6h4v4H4V6zm6 0h4v4h-4V6zm6 0h4v4h-4V6M4 14h4v4H4v-4zm6 0h4v4h-4v-4zm6 0h4v4h-4v-4z" />
                        </svg>
                    </button>
                    <button type="button" x-on:click="view = 'list'" class="p-2 rounded-lg"
                        :class="view === 'list' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-zinc-700'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <div x-show="open" x-transition class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <select wire:model="selectedTitle" class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600 dark:text-white">
                        <option value="">Tüm Unvanlar</option>
                        @foreach($titles as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>

                    <select wire:model="selectedDepartment" class="w-full px-4 py-3 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600 dark:text-white">
                        <option value="">Tüm Departmanlar</option>
                        @foreach($department as $d)
                            <option value="{{ $d }}">{{ $d }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-wrap gap-6 items-center">
                    <label class="inline-flex items-center space-x-2 text-gray-800 dark:text-gray-100">
                        <input type="checkbox" wire:model="hasPhone" class="rounded text-blue-600" />
                        <span>Telefonu olanlar</span>
                    </label>
                    <label class="inline-flex items-center space-x-2 text-gray-800 dark:text-gray-100">
                        <input type="checkbox" wire:model="hasEmail" class="rounded text-blue-600" />
                        <span>E-postası olanlar</span>
                    </label>
                </div>
            </div>
        </form>
    </div>

    @if($users->count())
        <div :class="view === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6' : 'flex flex-col gap-4'">
            @foreach($users as $user)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5 flex" :class="view === 'list' ? 'flex-row items-center space-x-4' : 'flex-col space-y-2'">
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">{{ $user->name }} {{ $user->surname }}</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-1">{{ $user->title }} - {{ $user->department }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-200">📞 {{ $user->phone ?? '—' }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-200">✉️ {{ $user->email ?? '—' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $users->links() }}</div>
    @elseif($query || $selectedTitle || $selectedDepartment || $hasPhone || $hasEmail)
        <p class="text-center text-gray-400 mt-8">Aradığınız kriterlere uygun kullanıcı bulunamadı.</p>
    @endif
</div>
