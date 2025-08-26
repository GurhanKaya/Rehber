@props([
    'defaultTab' => 0,
    'variant' => 'default', // default, pills, underline
    'size' => 'md', // sm, md, lg
    'fullWidth' => false,
    'vertical' => false
])

@php
    $variantClasses = [
        'default' => 'border-b border-gray-200 dark:border-gray-700',
        'pills' => 'bg-gray-100 dark:bg-gray-700 rounded-lg p-1',
        'underline' => 'border-b border-gray-200 dark:border-gray-700'
    ];

    $sizeClasses = [
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base'
    ];

    $baseClasses = 'transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500';
    $variantClass = $variantClasses[$variant];
    $sizeClass = $sizeClasses[$size];
    $widthClass = $fullWidth ? 'flex-1' : '';
    $directionClass = $vertical ? 'flex-col' : 'flex-row';
    
    $classes = $baseClasses . ' ' . $sizeClass . ' ' . $widthClass;
@endphp

<div 
    x-data="{ 
        activeTab: {{ $defaultTab }},
        tabs: [],
        init() {
            this.tabs = Array.from(this.$el.querySelectorAll('[data-tab]')).map((el, index) => ({
                id: el.dataset.tab,
                index: index
            }));
        }
    }"
    class="w-full"
>
    <!-- Tab Navigation -->
    <div class="{{ $variantClass }}">
        <nav class="flex {{ $directionClass }} space-x-1" role="tablist">
            {{ $navigation }}
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="mt-4">
        {{ $content }}
    </div>
</div>

@push('scripts')
<script>
    // Tab navigation item component
    window.TabItem = {
        props: ['tab', 'label', 'icon'],
        template: `
            <button
                :class="{
                    'bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 border-blue-500': activeTab === tab.index,
                    'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700': activeTab !== tab.index
                }"
                class="flex items-center justify-center {{ $classes }} {{ $variant === 'pills' ? 'rounded-md' : 'border-b-2 border-transparent' }}"
                @click="activeTab = tab.index"
                role="tab"
                :aria-selected="activeTab === tab.index"
            >
                <svg v-if="icon" class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-html="icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
                {{ label }}
            </button>
        `
    };

    // Tab content component
    window.TabContent = {
        props: ['tab'],
        template: `
            <div
                x-show="activeTab === tab.index"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform translate-y-2"
                role="tabpanel"
                :aria-labelledby="'tab-' + tab.index"
            >
                <slot></slot>
            </div>
        `
    };
</script>
@endpush
