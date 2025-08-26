<div class="max-w-3xl mx-auto bg-gray-900 border border-gray-700 rounded-xl p-6 shadow mt-10 text-white min-h-[400px]">
    <h2 class="text-2xl font-bold mb-4 text-center">Randevu Saatlerim</h2>

    <form wire:submit.prevent="addSlot" class="mb-4 flex flex-col md:flex-row gap-3 items-stretch md:items-center md:justify-center w-full">
        <select wire:model="day_of_week" class="border rounded p-2 text-black w-full min-w-[140px]">
            <option value="">Gün Seçiniz</option>
            <option value="1">Pazartesi</option>
            <option value="2">Salı</option>
            <option value="3">Çarşamba</option>
            <option value="4">Perşembe</option>
            <option value="5">Cuma</option>
            <option value="6">Cumartesi</option>
            <option value="0">Pazar</option>
        </select>
        <select wire:model="start_time" class="border rounded p-2 text-black w-full min-w-[140px]">
            <option value="">Başlangıç Saati</option>
            @for($h = 7; $h <= 18; $h++)
                <option value="{{ sprintf('%02d:00', $h) }}">{{ sprintf('%02d:00', $h) }}</option>
                <option value="{{ sprintf('%02d:30', $h) }}">{{ sprintf('%02d:30', $h) }}</option>
            @endfor
        </select>
        <select wire:model="end_time" class="border rounded p-2 text-black w-full min-w-[140px]">
            <option value="">Bitiş Saati</option>
            @for($h = 7; $h <= 18; $h++)
                <option value="{{ sprintf('%02d:00', $h) }}">{{ sprintf('%02d:00', $h) }}</option>
                <option value="{{ sprintf('%02d:30', $h) }}">{{ sprintf('%02d:30', $h) }}</option>
            @endfor
        </select>
        @if($editingSlotId)
            <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded w-full md:w-auto">Güncelle</button>
        @else
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full md:w-auto">Ekle</button>
        @endif
    </form>

    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-2 text-center">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="bg-red-100 text-red-800 p-2 rounded mb-2 text-center font-semibold">
            {{ session('error') }}
        </div>
    @endif
    
    @error('day_of_week') <div class="text-red-400 text-center">{{ $message }}</div> @enderror
    @error('start_time') <div class="text-red-400 text-center">{{ $message }}</div> @enderror
    @error('end_time') <div class="text-red-400 text-center">{{ $message }}</div> @enderror

    @if($editingSlotId)
        <div class="bg-blue-100 text-blue-800 p-2 rounded mb-2 text-center font-semibold">Randevu saatlerinizi değiştirip güncelleyebilirsiniz</div>
    @endif

    @php
        $days = ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi'];
        $grouped = collect($slots)->groupBy('day_of_week');
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 mt-6">
        @foreach($days as $i => $day)
            @if(isset($grouped[$i]) && count($grouped[$i]))
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-600 flex flex-col h-full w-full">
                    <div class="font-bold text-lg mb-2 text-center">{{ $day }}</div>
                    <div class="flex flex-row flex-wrap gap-2 justify-center">
                        @foreach($grouped[$i] as $slot)
                            <div class="flex items-center gap-2 text-base bg-gray-700 rounded px-3 py-1 min-w-[120px] shrink-0">
                                <span class="font-semibold">{{ substr($slot->start_time,0,5) }} - {{ substr($slot->end_time,0,5) }}</span>
                                <button wire:click="deleteSlot({{ $slot->id }})" class="bg-red-500 hover:bg-red-700 text-white px-2 py-0.5 rounded text-xs">Sil</button>
                                <button wire:click="editSlot({{ $slot->id }})" class="bg-blue-500 hover:bg-blue-700 text-white px-2 py-0.5 rounded text-xs">Düzenle</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
        @if($grouped->isEmpty())
            <div class="text-center text-gray-400 mt-8">Henüz randevu saati eklenmedi.</div>
        @endif
    </div>
</div>
