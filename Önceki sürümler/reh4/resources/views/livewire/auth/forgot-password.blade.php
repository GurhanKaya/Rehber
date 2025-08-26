 <div class="flex flex-col gap-6">
    <x-auth-header :title="__('auth.forgot_password_title')" :description="__('auth.forgot_password_description')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('auth.email_address')"
            type="email"
            required
            autofocus
            placeholder="ornek@email.com"
        />

        <flux:button variant="primary" type="submit" class="w-full">{{ __('auth.email_password_reset_link') }}</flux:button>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
        <span>{{ __('auth.or_return_to') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('auth.back_to_login') }}</flux:link>
    </div>
</div>
