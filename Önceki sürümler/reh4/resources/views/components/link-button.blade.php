@props([
    'href' => '#',
    'variant' => 'primary', // primary, secondary, danger, warning, success, info
    'size' => 'md', // sm, md, lg
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'left', // left, right
    'fullWidth' => false,
    'external' => false,
    'method' => 'GET', // GET, POST, PUT, DELETE
    'wire:navigate' => false
])

@php
    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base'
    ];

    $variantClasses = [
        'primary' => 'text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 focus:ring-blue-500',
        'secondary' => 'text-gray-600 hover:text-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 focus:ring-gray-500',
        'danger' => 'text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 focus:ring-red-500',
        'warning' => 'text-yellow-600 hover:text-yellow-700 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 focus:ring-yellow-500',
        'success' => 'text-green-600 hover:text-green-700 hover:bg-green-50 dark:hover:bg-green-900/20 focus:ring-green-500',
        'info' => 'text-blue-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 focus:ring-blue-400'
    ];

    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed';
    $sizeClass = $sizeClasses[$size];
    $variantClass = $variantClasses[$variant];
    $widthClass = $fullWidth ? 'w-full' : '';
    
    $classes = $baseClasses . ' ' . $sizeClass . ' ' . $variantClass . ' ' . $widthClass;
    
    // Wire navigate attribute
    $wireNavigate = $wire_navigate ? 'wire:navigate' : '';
@endphp

@if($method === 'GET')
    <a 
        href="{{ $href }}"
        @if($external) target="_blank" rel="noopener noreferrer" @endif
        @if($disabled) aria-disabled="true" @endif
        {!! $wireNavigate !!}
        class="{{ $classes }} @if($disabled) pointer-events-none @endif"
        {{ $attributes->merge(['class' => '']) }}
    >
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
        
        @if($external)
            <svg class="h-4 w-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
        @endif
    </a>
@else
    <form method="POST" action="{{ $href }}" class="inline">
        @csrf
        @if($method !== 'POST')
            @method($method)
        @endif
        
        <button 
            type="submit"
            @if($disabled) disabled @endif
            class="{{ $classes }}"
            {{ $attributes->merge(['class' => '']) }}
        >
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
        </button>
    </form>
@endif
