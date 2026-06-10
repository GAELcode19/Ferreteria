<div class="flex flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">Reportes</flux:heading>
            <flux:text class="mt-1">Análisis de ventas e inventario</flux:text>
        </div>
        <div class="flex items-end gap-3">
            <flux:input type="date" label="Desde" wire:model.live="desde" />
            <flux:input type="date" label="Hasta" wire:model.live="hasta" />
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
            <flux:text class="text-sm font-medium">Total vendido</flux:text>
            <div class="mt-2 text-2xl font-bold text-stone-900">${{ number_format((float) $totalVendido, 2) }}</div>
        </div>
        <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
            <flux:text class="text-sm font-medium">Número de ventas</flux:text>
            <div class="mt-2 text-2xl font-bold text-stone-900">{{ number_format($numVentas) }}</div>
        </div>
        <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
            <flux:text class="text-sm font-medium">Ticket promedio</flux:text>
            <div class="mt-2 text-2xl font-bold text-stone-900">${{ number_format((float) $ticketPromedio, 2) }}</div>
        </div>
        <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
            <flux:text class="text-sm font-medium">Valor del inventario (costo)</flux:text>
            <div class="mt-2 text-2xl font-bold text-stone-900">${{ number_format((float) $valorInventario, 2) }}</div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        {{-- Productos más vendidos --}}
        <div class="rounded-xl border border-stone-200 bg-white shadow-sm">
            <div class="border-b border-stone-100 px-5 py-4">
                <flux:heading size="lg">Productos más vendidos</flux:heading>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-100 text-left text-xs uppercase tracking-wide text-stone-500">
                        <th class="px-5 py-3 font-medium">#</th>
                        <th class="px-5 py-3 font-medium">Producto</th>
                        <th class="px-5 py-3 text-center font-medium">Unidades</th>
                        <th class="px-5 py-3 text-right font-medium">Importe</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($masVendidos as $i => $producto)
                        <tr class="border-b border-stone-50">
                            <td class="px-5 py-3 text-stone-400">{{ $i + 1 }}</td>
                            <td class="px-5 py-3">
                                <div class="font-medium text-stone-900">{{ $producto->nombre }}</div>
                                <div class="text-xs text-stone-500">{{ $producto->codigo }}</div>
                            </td>
                            <td class="px-5 py-3 text-center font-semibold text-stone-700">{{ number_format($producto->unidades) }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-stone-900">${{ number_format((float) $producto->importe, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-stone-400">Sin ventas en el periodo seleccionado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-6">
            {{-- Ventas por método de pago --}}
            <div class="rounded-xl border border-stone-200 bg-white shadow-sm">
                <div class="border-b border-stone-100 px-5 py-4">
                    <flux:heading size="lg">Ventas por método de pago</flux:heading>
                </div>
                <div class="divide-y divide-stone-50">
                    @forelse ($porMetodo as $metodo)
                        <div class="flex items-center justify-between px-5 py-3">
                            <div class="flex items-center gap-3">
                                <flux:icon.credit-card class="size-5 text-stone-400" />
                                <span class="font-medium text-stone-900">{{ ucfirst($metodo->metodo_pago) }}</span>
                                <flux:badge size="sm" color="zinc">{{ $metodo->cantidad }}</flux:badge>
                            </div>
                            <span class="font-semibold text-stone-900">${{ number_format((float) $metodo->total, 2) }}</span>
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center text-stone-400">Sin datos en el periodo.</div>
                    @endforelse
                </div>
            </div>

            {{-- Ventas por vendedor --}}
            <div class="rounded-xl border border-stone-200 bg-white shadow-sm">
                <div class="border-b border-stone-100 px-5 py-4">
                    <flux:heading size="lg">Ventas por vendedor</flux:heading>
                </div>
                <div class="divide-y divide-stone-50">
                    @forelse ($porVendedor as $vendedor)
                        <div class="flex items-center justify-between px-5 py-3">
                            <div class="flex items-center gap-3">
                                <flux:icon.user class="size-5 text-stone-400" />
                                <span class="font-medium text-stone-900">{{ $vendedor->name }}</span>
                                <flux:badge size="sm" color="zinc">{{ $vendedor->cantidad }} ventas</flux:badge>
                            </div>
                            <span class="font-semibold text-stone-900">${{ number_format((float) $vendedor->total, 2) }}</span>
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center text-stone-400">Sin datos en el periodo.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
