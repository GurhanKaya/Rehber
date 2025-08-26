@props([
    'name' => '',
    'id' => null,
    'label' => null,
    'value' => '1',
    'checked' => false,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'size' => 'md', // sm, md, lg
    'error' => null,
    'help' => null,
    'wire_model' => null,
    'wire_model_live' => null,
    'wire_model_defer' => null
])

@php
    $id = $id ?? $name;
    
    $sizeClasses = [
        'sm' => 'h-4 w-4',
        'md' => 'h-4 w-4',
        'lg' => 'h-5 w-5'
    ];

    $baseClasses = 'rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200';
    $sizeClass = $sizeClasses[$size];
    
    $stateClasses = 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500';
    
    if ($error) {
        $stateClasses = 'bg-red-50 dark:bg-red-900/20 border-red-300 dark:border-red-600 text-red-600 focus:ring-red-500';
    }
    
    if ($disabled) {
        $stateClasses = 'bg-gray-100 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-400 cursor-not-allowed';
    }
    
    $classes = $baseClasses . ' ' . $sizeClass . ' ' . $stateClasses;
    
    // Wire model attributes
    $wireAttributes = '';
    if (isset($wire_model)) $wireAttributes .= ' wire:model="' . $wire_model . '"';
    if (isset($wire_model_live)) $wireAttributes .= ' wire:model.live="' . $wire_model_live . '"';
    if (isset($wire_model_defer)) $wireAttributes .= ' wire:model.defer="' . $wire_model_defer . '"';
@endphp

<div class="flex items-start">
    <div class="flex items-center h-5">
        <input 
            type="checkbox"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ $value }}"
            @if($checked) checked @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            {!! $wireAttributes !!}
            class="{{ $classes }}"
            {{ $attributes->merge(['class' => '']) }}
        >
    </div>
    
    <div class="ml-3 text-sm">
        @if($label)
            <label for="{{ $id }}" class="font-medium text-gray-700 dark:text-gray-300">
                {{ $label }}
                @if($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
        @endif

        @if($help && !$error)
            <p class="text-gray-500 dark:text-gray-400">{{ $help }}</p>
        @endif

        @if($error)
            <p class="text-red-600 dark:text-red-400">{{ $error }}</p>
        @endif
    </div>
</div>
