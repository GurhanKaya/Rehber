@props([
    'trigger' => null,
    'placement' => 'bottom', // top, bottom, left, right
    'width' => 'default', // sm, default, lg, xl
    'align' => 'left', // left, right, center
    'persistent' => false
])

@php
    $placementClasses = [
        'top' => 'bottom-full mb-2',
        'bottom' => 'top-full mt-2',
        'left' => 'right-full mr-2',
        'right' => 'left-full ml-2'
    ];

    $widthClasses = [
        'sm' => 'w-32',
        'default' => 'w-48',
        'lg' => 'w-64',
        'xl' => 'w-80'
    ];

    $alignClasses = [
        'left' => 'left-0',
        'right' => 'right-0',
        'center' => 'left-1/2 transform -translate-x-1/2'
    ];

    $placementClass = $placementClasses[$placement];
    $widthClass = $widthClasses[$width];
    $alignClass = $alignClasses[$align];
@endphp

<div 
    x-data="{ open: false }" 
    @click.away="open = false"
    @keydown.escape="open = false"
    class="relative"
>
    <!-- Trigger -->
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    <!-- Dropdown Menu -->
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 {{ $placementClass }} {{ $alignClass }} {{ $widthClass }}"
        style="display: none;"
    >
        <div class="bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 py-1">
            {{ $slot }}
        </div>
    </div>
</div>
