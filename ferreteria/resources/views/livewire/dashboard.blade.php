<div class="flex flex-col gap-6">
    <div>
        <flux:heading size="xl">Panel principal</flux:heading>
        <flux:text class="mt-1">Resumen general de la ferretería — {{ now()->translatedFormat('l d \d\e F \d\e Y') }}</flux:text>
    </div>

    {{-- Tarjetas de estadísticas --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <flux:text class="text-sm font-medium">Ventas de hoy</flux:text>
                <div class="flex size-9 items-center justify-center rounded-lg bg-emerald-100">
                    <flux:icon.banknotes class="size-5 text-emerald-700" />
                </div>
            </div>
            <div class="mt-2 text-2xl font-bold text-stone-900">${{ number_format((float) $ventasHoy, 2) }}</div>
            <flux:text class="mt-1 text-xs">{{ $numVentasHoy }} {{ Str::plural('venta', $numVentasHoy) }} registradas</flux:text>
        </div>

        <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <flux:text class="text-sm font-medium">Ventas del mes</flux:text>
                <div class="flex size-9 items-center justify-center rounded-lg bg-sky-100">
                    <flux:icon.chart-bar class="size-5 text-sky-700" />
                </div>
            </div>
            <div class="mt-2 text-2xl font-bold text-stone-900">${{ number_format((float) $ventasMes, 2) }}</div>
            <flux:text class="mt-1 text-xs">{{ now()->translatedFormat('F Y') }}</flux:text>
        </div>

        <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <flux:text class="text-sm font-medium">Productos activos</flux:text>
                <div class="flex size-9 items-center justify-center rounded-lg bg-amber-100">
                    <flux:icon.cube class="size-5 text-amber-700" />
                </div>
            </div>
            <div class="mt-2 text-2xl font-bold text-stone-900">{{ number_format($totalProductos) }}</div>
            <flux:text class="mt-1 text-xs">en catálogo</flux:text>
        </div>

        <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <flux:text class="text-sm font-medium">Stock bajo</flux:text>
                <div class="flex size-9 items-center justify-center rounded-lg {{ $stockBajo > 0 ? 'bg-red-100' : 'bg-stone-100' }}">
                    <flux:icon.exclamation-triangle class="size-5 {{ $stockBajo > 0 ? 'text-red-700' : 'text-stone-500' }}" />
                </div>
            </div>
            <div class="mt-2 text-2xl font-bold {{ $stockBajo > 0 ? 'text-red-700' : 'text-stone-900' }}">{{ $stockBajo }}</div>
            <flux:text class="mt-1 text-xs">{{ Str::plural('producto', $stockBajo) }} por resurtir</flux:text>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-5">
        {{-- Últimas ventas --}}
        <div class="rounded-xl border border-stone-200 bg-white shadow-sm xl:col-span-3">
            <div class="flex items-center justify-between border-b border-stone-100 px-5 py-4">
                <flux:heading size="lg">Últimas ventas</flux:heading>
                <flux:button :href="route('ventas.index')" variant="subtle" size="sm" icon-trailing="arrow-right" wire:navigate>Ver todas</flux:button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-stone-100 text-left text-xs uppercase tracking-wide text-stone-500">
                            <th class="px-5 py-3 font-medium">Folio</th>
                            <th class="px-5 py-3 font-medium">Cliente</th>
                            <th class="px-5 py-3 font-medium">Atendió</th>
                            <th class="px-5 py-3 font-medium">Fecha</th>
                            <th class="px-5 py-3 text-right font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ultimasVentas as $venta)
                            <tr class="border-b border-stone-50 hover:bg-stone-50">
                                <td class="px-5 py-3 font-medium text-stone-900">{{ $venta->folio }}</td>
                                <td class="px-5 py-3 text-stone-600">{{ $venta->cliente?->nombre ?? 'Público general' }}</td>
                                <td class="px-5 py-3 text-stone-600">{{ $venta->usuario->name }}</td>
                                <td class="px-5 py-3 text-stone-500">{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-3 text-right font-semibold text-stone-900">${{ number_format((float) $venta->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-stone-400">Aún no hay ventas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Stock bajo --}}
        <div class="rounded-xl border border-stone-200 bg-white shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between border-b border-stone-100 px-5 py-4">
                <flux:heading size="lg">Productos por resurtir</flux:heading>
                <flux:button :href="route('productos.index')" variant="subtle" size="sm" icon-trailing="arrow-right" wire:navigate>Inventario</flux:button>
            </div>
            <div class="divide-y divide-stone-50">
                @forelse ($productosStockBajo as $producto)
                    <div class="flex items-center justify-between px-5 py-3">
                        <div class="min-w-0">
                            <div class="truncate font-medium text-stone-900">{{ $producto->nombre }}</div>
                            <div class="text-xs text-stone-500">{{ $producto->codigo }}</div>
                        </div>
                        <flux:badge color="{{ $producto->stock === 0 ? 'red' : 'amber' }}" size="sm">
                            {{ $producto->stock }} / mín. {{ $producto->stock_minimo }}
                        </flux:badge>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-stone-400">Todo el inventario está en orden. ✔</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
