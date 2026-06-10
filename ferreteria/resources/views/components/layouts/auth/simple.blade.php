<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#F7F3EB] antialiased">
        <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-2">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="mb-1 flex size-12 items-center justify-center rounded-xl bg-amber-600 text-white shadow-md">
                        <flux:icon.wrench-screwdriver class="size-7" />
                    </span>
                    <span class="text-lg font-bold text-stone-900">{{ config('app.name') }}</span>
                    <span class="-mt-1 text-xs text-stone-500">Sistema de gestión</span>
                </a>
                <div class="mt-2 flex flex-col gap-6 rounded-xl border border-stone-200 bg-white p-6 shadow-sm">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
