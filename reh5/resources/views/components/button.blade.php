@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, danger, warning, success, info
    'size' => 'md', // sm, md, lg
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'iconPosition' => 'left', // left, right
    'fullWidth' => false,
    'wire:click' => null,
    'wire:loading' => null,
    'wire:target' => null
])

@php
    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base'
    ];

    $variantClasses = [
        'primary' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 text-white border-transparent',
        'secondary' => 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500 text-white border-transparent',
        'danger' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white border-transparent',
        'warning' => 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500 text-white border-transparent',
        'success' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500 text-white border-transparent',
        'info' => 'bg-blue-500 hover:bg-blue-600 focus:ring-blue-400 text-white border-transparent'
    ];

    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed';
    $sizeClass = $sizeClasses[$size];
    $variantClass = $variantClasses[$variant];
    $widthClass = $fullWidth ? 'w-full' : '';
    
    $classes = $baseClasses . ' ' . $sizeClass . ' ' . $variantClass . ' ' . $widthClass;
    
    // Wire attributes
    $wireAttributes = '';
    if (isset($wire_click)) $wireAttributes .= ' wire:click="' . $wire_click . '"';
    if (isset($wire_loading)) $wireAttributes .= ' wire:loading="' . $wire_loading . '"';
    if (isset($wire_target)) $wireAttributes .= ' wire:target="' . $wire_target . '"';
@endphp

<button 
    type="{{ $type }}"
    @if($disabled) disabled @endif
    {!! $wireAttributes !!}
    class="{{ $classes }}"
    {{ $attributes->merge(['class' => '']) }}
>
    @if($loading)
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Yükleniyor...
    @else
        @if($icon && $iconPosition === 'left')
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icon !!}
            </svg>
        @endif
        
        {{ $slot }}
        
        @if($icon && $iconPosition === 'right')
            <svg class="h-4 w-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icon !!}
            </svg>
        @endif
    @endif
</button>
