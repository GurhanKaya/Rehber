<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex items-center justify-center">
        <div class="flex min-h-screen w-full items-center justify-center p-4">
            <div class="w-full max-w-md">
                <div class="flex flex-col items-center gap-4 mb-8">
                    <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                        <span class="flex h-16 w-16 items-center justify-center rounded-full bg-white/10 shadow-lg">
                            <x-app-logo-icon class="size-12 fill-current text-white" />
                        </span>
                        <span class="text-lg font-bold text-white tracking-wide select-none">Rehber</span>
                    </a>
                </div>
                <div class="rounded-2xl border border-zinc-700 bg-zinc-900/80 shadow-2xl p-8 backdrop-blur-md">
                    <div class="flex flex-col gap-6">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
        @fluxScripts
        <script> //login sonrası, login sayfasına dönemmesini engelleme çalışmları
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    window.location.reload();
                }
            });
        </script>
    </body>
</html>
