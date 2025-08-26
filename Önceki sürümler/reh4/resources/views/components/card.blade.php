@props([
    'variant' => 'default', // default, elevated, outlined, flat
    'padding' => 'default', // none, sm, default, lg, xl
    'rounded' => 'default', // none, sm, default, lg, xl, full
    'shadow' => 'default', // none, sm, default, lg, xl
    'hover' => false,
    'clickable' => false
])

@php
    $variantClasses = [
        'default' => 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700',
        'elevated' => 'bg-white dark:bg-gray-800 shadow-lg border-0',
        'outlined' => 'bg-transparent border-2 border-gray-200 dark:border-gray-700',
        'flat' => 'bg-gray-50 dark:bg-gray-900 border-0'
    ];

    $paddingClasses = [
        'none' => '',
        'sm' => 'p-3',
        'default' => 'p-6',
        'lg' => 'p-8',
        'xl' => 'p-10'
    ];

    $roundedClasses = [
        'none' => 'rounded-none',
        'sm' => 'rounded-sm',
        'default' => 'rounded-lg',
        'lg' => 'rounded-xl',
        'xl' => 'rounded-2xl',
        'full' => 'rounded-full'
    ];

    $shadowClasses = [
        'none' => 'shadow-none',
        'sm' => 'shadow-sm',
        'default' => 'shadow',
        'lg' => 'shadow-lg',
        'xl' => 'shadow-xl'
    ];

    $baseClasses = 'transition-all duration-200';
    $variantClass = $variantClasses[$variant];
    $paddingClass = $paddingClasses[$padding];
    $roundedClass = $roundedClasses[$rounded];
    $shadowClass = $shadowClasses[$shadow];
    
    $hoverClasses = $hover ? 'hover:shadow-lg hover:-translate-y-1' : '';
    $clickableClasses = $clickable ? 'cursor-pointer hover:shadow-lg hover:-translate-y-1 active:translate-y-0' : '';
    
    $classes = $baseClasses . ' ' . $variantClass . ' ' . $paddingClass . ' ' . $roundedClass . ' ' . $shadowClass . ' ' . $hoverClasses . ' ' . $clickableClasses;
@endphp

<div class="{{ $classes }}" {{ $attributes->merge(['class' => '']) }}>
    {{ $slot }}
</div>
