@props([
    'variant' => 'default', // default, primary, secondary, success, warning, danger, info
    'size' => 'md', // sm, md, lg
    'rounded' => 'default', // none, sm, default, full
    'dismissible' => false,
    'wire:click' => null
])

@php
    $variantClasses = [
        'default' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
        'primary' => 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200',
        'secondary' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
        'success' => 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-200',
        'warning' => 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200',
        'danger' => 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-200',
        'info' => 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200'
    ];

    $sizeClasses = [
        'sm' => 'px-2 py-1 text-xs',
        'md' => 'px-2.5 py-1.5 text-sm',
        'lg' => 'px-3 py-2 text-base'
    ];

    $roundedClasses = [
        'none' => 'rounded-none',
        'sm' => 'rounded-sm',
        'default' => 'rounded-full',
        'full' => 'rounded-full'
    ];

    $baseClasses = 'inline-flex items-center font-medium transition-colors duration-200';
    $variantClass = $variantClasses[$variant];
    $sizeClass = $sizeClasses[$size];
    $roundedClass = $roundedClasses[$rounded];
    
    $classes = $baseClasses . ' ' . $variantClass . ' ' . $sizeClass . ' ' . $roundedClass;
    
    // Wire attributes
    $wireAttributes = '';
    if (isset($wire_click)) $wireAttributes .= ' wire:click="' . $wire_click . '"';
@endphp

<span 
    {!! $wireAttributes !!}
    class="{{ $classes }}"
    {{ $attributes->merge(['class' => '']) }}
>
    {{ $slot }}
    
    @if($dismissible)
        <button 
            type="button"
            class="ml-1.5 -mr-1 h-4 w-4 rounded-full inline-flex items-center justify-center text-current hover:bg-current hover:bg-opacity-20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-transparent"
            aria-label="Kapat"
        >
            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    @endif
</span>
