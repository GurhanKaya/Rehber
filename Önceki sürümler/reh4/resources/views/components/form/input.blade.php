@props([
    'type' => 'text',
    'name' => '',
    'id' => null,
    'label' => null,
    'placeholder' => null,
    'value' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'autofocus' => false,
    'autocomplete' => null,
    'size' => 'md', // sm, md, lg
    'error' => null,
    'help' => null,
    'prefix' => null,
    'suffix' => null,
    'wire_model' => null,
    'wire_model_live' => null,
    'wire_model_debounce' => null,
    'wire_model_defer' => null
])

@php
    $id = $id ?? $name;
    
    $sizeClasses = [
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-3 py-2 text-sm',
        'lg' => 'px-4 py-3 text-base'
    ];

    $baseClasses = 'block w-full border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200';
    $sizeClass = $sizeClasses[$size];
    
    $stateClasses = 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500';
    
    if ($error) {
        $stateClasses = 'border-red-300 dark:border-red-600 bg-red-50 dark:bg-red-900/20 text-red-900 dark:text-red-100 placeholder-red-400 dark:placeholder-red-300 focus:border-red-500 focus:ring-red-500';
    }
    
    if ($disabled) {
        $stateClasses = 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 cursor-not-allowed';
    }
    
    $classes = $baseClasses . ' ' . $sizeClass . ' ' . $stateClasses;
    
    // Wire model attributes
    $wireAttributes = '';
    if (isset($wire_model)) $wireAttributes .= ' wire:model="' . $wire_model . '"';
    if (isset($wire_model_live)) $wireAttributes .= ' wire:model.live="' . $wire_model_live . '"';
    if (isset($wire_model_debounce)) $wireAttributes .= ' wire:model.debounce="' . $wire_model_debounce . '"';
    if (isset($wire_model_defer)) $wireAttributes .= ' wire:model.defer="' . $wire_model_defer . '"';
@endphp

<div>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($prefix)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 dark:text-gray-400 text-sm">{{ $prefix }}</span>
            </div>
        @endif

        <input 
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($value !== null) value="{{ $value }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($autofocus) autofocus @endif
            @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            {!! $wireAttributes !!}
            class="{{ $classes }} @if($prefix) pl-10 @endif @if($suffix) pr-10 @endif"
            {{ $attributes->merge(['class' => '']) }}
        >

        @if($suffix)
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <span class="text-gray-500 dark:text-gray-400 text-sm">{{ $suffix }}</span>
            </div>
        @endif
    </div>

    @if($error)
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif

    @if($help && !$error)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $help }}</p>
    @endif
</div>
