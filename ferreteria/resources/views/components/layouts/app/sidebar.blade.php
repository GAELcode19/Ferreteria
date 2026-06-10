<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#F7F3EB]">
        <flux:sidebar sticky stashable class="border-r border-stone-200 bg-[#F2EDE2]">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="mr-5 flex items-center space-x-2" wire:navigate>
                <x-app-logo class="size-8" href="#"></x-app-logo>
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group heading="General" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>Panel principal</flux:navlist.item>
                    <flux:navlist.item icon="shopping-cart" :href="route('ventas.crear')" :current="request()->routeIs('ventas.crear')" wire:navigate>Punto de venta</flux:navlist.item>
                    <flux:navlist.item icon="clipboard-document-list" :href="route('ventas.index')" :current="request()->routeIs('ventas.index')" wire:navigate>Ventas</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group heading="Catálogo" class="grid">
                    <flux:navlist.item icon="cube" :href="route('productos.index')" :current="request()->routeIs('productos.index')" wire:navigate>Productos</flux:navlist.item>
                    <flux:navlist.item icon="users" :href="route('clientes.index')" :current="request()->routeIs('clientes.index')" wire:navigate>Clientes</flux:navlist.item>
                </flux:navlist.group>

                @if (auth()->user()->isAdmin())
                    <flux:navlist.group heading="Administración" class="grid">
                        <flux:navlist.item icon="tag" :href="route('categorias.index')" :current="request()->routeIs('categorias.index')" wire:navigate>Categorías</flux:navlist.item>
                        <flux:navlist.item icon="truck" :href="route('proveedores.index')" :current="request()->routeIs('proveedores.index')" wire:navigate>Proveedores</flux:navlist.item>
                        <flux:navlist.item icon="user-group" :href="route('usuarios.index')" :current="request()->routeIs('usuarios.index')" wire:navigate>Usuarios</flux:navlist.item>
                        <flux:navlist.item icon="chart-bar" :href="route('reportes.index')" :current="request()->routeIs('reportes.index')" wire:navigate>Reportes</flux:navlist.item>
                    </flux:navlist.group>
                @endif
            </flux:navlist>

            <flux:spacer />

            {{-- Menú de usuario (escritorio) --}}
            <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span class="flex h-full w-full items-center justify-center rounded-lg bg-amber-100 text-amber-800">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->isAdmin() ? 'Administrador' : 'Empleado' }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item href="/settings/profile" icon="cog" wire:navigate>Mi cuenta</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            Cerrar sesión
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Menú de usuario (móvil) -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span class="flex h-full w-full items-center justify-center rounded-lg bg-amber-100 text-amber-800">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->isAdmin() ? 'Administrador' : 'Empleado' }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item href="/settings/profile" icon="cog" wire:navigate>Mi cuenta</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            Cerrar sesión
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
