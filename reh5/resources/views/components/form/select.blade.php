@props([
    'name' => '',
    'id' => null,
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => 'Seçiniz',
    'required' => false,
    'disabled' => false,
    'multiple' => false,
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
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-3 py-2 text-sm',
        'lg' => 'px-4 py-3 text-base'
    ];

    $baseClasses = 'block w-full border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200';
    $sizeClass = $sizeClasses[$size];
    
    $stateClasses = 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500';
    
    if ($error) {
        $stateClasses = 'border-red-300 dark:border-red-600 bg-red-50 dark:bg-red-900/20 text-red-900 dark:text-red-100 focus:border-red-500 focus:ring-red-500';
    }
    
    if ($disabled) {
        $stateClasses = 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 cursor-not-allowed';
    }
    
    $classes = $baseClasses . ' ' . $sizeClass . ' ' . $stateClasses;
    
    // Wire model attributes
    $wireAttributes = '';
    if (isset($wire_model)) $wireAttributes .= ' wire:model="' . $wire_model . '"';
    if (isset($wire_model_live)) $wireAttributes .= ' wire:model.live="' . $wire_model_live . '"';
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

    <select 
        name="{{ $name }}"
        id="{{ $id }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($multiple) multiple @endif
        {!! $wireAttributes !!}
        class="{{ $classes }}"
        {{ $attributes->merge(['class' => '']) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $value => $label)
            <option 
                value="{{ $value }}"
                @if($multiple)
                    @if(is_array($selected) && in_array($value, $selected)) selected @endif
                @else
                    @if($selected == $value) selected @endif
                @endif
            >
                {{ $label }}
            </option>
        @endforeach
    </select>

    @if($error)
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif

    @if($help && !$error)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $help }}</p>
    @endif
</div>
