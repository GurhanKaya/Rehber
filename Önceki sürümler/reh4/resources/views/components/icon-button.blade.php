@props([
    'type' => 'button',
    'variant' => 'secondary', // primary, secondary, danger, warning, success, info, ghost
    'size' => 'md', // sm, md, lg
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'tooltip' => null,
    'wire:click' => null,
    'wire:loading' => null,
    'wire:target' => null
])

@php
    $sizeClasses = [
        'sm' => 'p-1.5',
        'md' => 'p-2',
        'lg' => 'p-3'
    ];

    $iconSizes = [
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
        'lg' => 'h-6 w-6'
    ];

    $variantClasses = [
        'primary' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 text-white border-transparent',
        'secondary' => 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500 text-white border-transparent',
        'danger' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white border-transparent',
        'warning' => 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500 text-white border-transparent',
        'success' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500 text-white border-transparent',
        'info' => 'bg-blue-500 hover:bg-blue-600 focus:ring-blue-400 text-white border-transparent',
        'ghost' => 'bg-transparent hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 border-gray-300 dark:border-gray-600'
    ];

    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed';
    $sizeClass = $sizeClasses[$size];
    $iconSizeClass = $iconSizes[$size];
    $variantClass = $variantClasses[$variant];
    
    $classes = $baseClasses . ' ' . $sizeClass . ' ' . $variantClass;
    
    // Wire attributes
    $wireAttributes = '';
    if (isset($wire_click)) $wireAttributes .= ' wire:click="' . $wire_click . '"';
    if (isset($wire_loading)) $wireAttributes .= ' wire:loading="' . $wire_loading . '"';
    if (isset($wire_target)) $wireAttributes .= ' wire:target="' . $wire_target . '"';
@endphp

<div class="relative" x-data="{ showTooltip: false }">
    <button 
        type="{{ $type }}"
        @if($disabled) disabled @endif
        @if($tooltip) 
            @mouseenter="showTooltip = true" 
            @mouseleave="showTooltip = false"
        @endif
        {!! $wireAttributes !!}
        class="{{ $classes }}"
        {{ $attributes->merge(['class' => '']) }}
    >
        @if($loading)
            <svg class="animate-spin {{ $iconSizeClass }}" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @elseif($icon)
            <svg class="{{ $iconSizeClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icon !!}
            </svg>
        @else
            {{ $slot }}
        @endif
    </button>

    @if($tooltip)
        <div 
            x-show="showTooltip"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute z-50 px-2 py-1 text-xs text-white bg-gray-900 rounded-md shadow-lg -top-8 left-1/2 transform -translate-x-1/2 whitespace-nowrap"
        >
            {{ $tooltip }}
            <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
        </div>
    @endif
</div>
