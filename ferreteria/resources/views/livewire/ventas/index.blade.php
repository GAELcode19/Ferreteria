<div class="flex flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">Ventas</flux:heading>
            <flux:text class="mt-1">Historial de ventas realizadas</flux:text>
        </div>
        <flux:button variant="primary" icon="plus" :href="route('ventas.crear')" wire:navigate>Nueva venta</flux:button>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <div class="w-full max-w-xs">
            <flux:input icon="magnifying-glass" placeholder="Buscar por folio o cliente…" wire:model.live.debounce.300ms="busqueda" clearable />
        </div>
        <div class="w-full max-w-48">
            <flux:input type="date" wire:model.live="fecha" />
        </div>
        @if ($fecha)
            <flux:button variant="subtle" size="sm" icon="x-mark" wire:click="$set('fecha', '')">Limpiar fecha</flux:button>
        @endif
    </div>

    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-100 bg-stone-50/60 text-left text-xs uppercase tracking-wide text-stone-500">
                        <th class="px-5 py-3 font-medium">Folio</th>
                        <th class="px-5 py-3 font-medium">Cliente</th>
                        <th class="px-5 py-3 font-medium">Atendió</th>
                        <th class="px-5 py-3 font-medium">Fecha</th>
                        <th class="px-5 py-3 font-medium">Pago</th>
                        <th class="px-5 py-3 text-center font-medium">Estado</th>
                        <th class="px-5 py-3 text-right font-medium">Total</th>
                        <th class="px-5 py-3 text-right font-medium">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ventas as $venta)
                        <tr class="border-b border-stone-50 hover:bg-stone-50 {{ $venta->estado === 'anulada' ? 'opacity-60' : '' }}" wire:key="venta-{{ $venta->id }}">
                            <td class="px-5 py-3 font-mono text-xs font-semibold text-stone-900">{{ $venta->folio }}</td>
                            <td class="px-5 py-3 text-stone-600">{{ $venta->cliente?->nombre ?? 'Público general' }}</td>
                            <td class="px-5 py-3 text-stone-600">{{ $venta->usuario->name }}</td>
                            <td class="px-5 py-3 text-stone-500">{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3 text-stone-600">{{ ucfirst($venta->metodo_pago) }}</td>
                            <td class="px-5 py-3 text-center">
                                <flux:badge size="sm" color="{{ $venta->estado === 'completada' ? 'emerald' : 'red' }}">{{ ucfirst($venta->estado) }}</flux:badge>
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-stone-900">${{ number_format((float) $venta->total, 2) }}</td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <flux:button size="sm" variant="subtle" icon="eye" wire:click="verDetalle({{ $venta->id }})" tooltip="Ver detalle" />
                                    @if (auth()->user()->isAdmin() && $venta->estado === 'completada')
                                        <flux:button size="sm" variant="subtle" icon="arrow-uturn-left" wire:click="anular({{ $venta->id }})" wire:confirm="¿Anular la venta {{ $venta->folio }}? El stock será devuelto al inventario." tooltip="Anular venta" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-stone-400">No hay ventas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-stone-100 px-5 py-3">
            {{ $ventas->links() }}
        </div>
    </div>

    {{-- Modal detalle de venta --}}
    <flux:modal name="venta-detalle" class="w-full max-w-2xl">
        @if ($detalle)
            <div class="space-y-5">
                <div class="flex items-start justify-between">
                    <div>
                        <flux:heading size="lg">Venta {{ $detalle->folio }}</flux:heading>
                        <flux:text class="mt-1">{{ $detalle->created_at->format('d/m/Y H:i') }} · Atendió {{ $detalle->usuario->name }}</flux:text>
                    </div>
                    <flux:badge color="{{ $detalle->estado === 'completada' ? 'emerald' : 'red' }}">{{ ucfirst($detalle->estado) }}</flux:badge>
                </div>

                <div class="grid gap-3 rounded-lg bg-stone-50 p-4 text-sm sm:grid-cols-2">
                    <div>
                        <span class="text-stone-500">Cliente:</span>
                        <span class="font-medium text-stone-900">{{ $detalle->cliente?->nombre ?? 'Público general' }}</span>
                    </div>
                    <div>
                        <span class="text-stone-500">Método de pago:</span>
                        <span class="font-medium text-stone-900">{{ ucfirst($detalle->metodo_pago) }}</span>
                    </div>
                </div>

                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-stone-100 text-left text-xs uppercase tracking-wide text-stone-500">
                            <th class="py-2 font-medium">Producto</th>
                            <th class="py-2 text-center font-medium">Cant.</th>
                            <th class="py-2 text-right font-medium">P. unitario</th>
                            <th class="py-2 text-right font-medium">Importe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detalle->detalles as $linea)
                            <tr class="border-b border-stone-50">
                                <td class="py-2 text-stone-900">{{ $linea->producto->nombre }}</td>
                                <td class="py-2 text-center text-stone-600">{{ $linea->cantidad }}</td>
                                <td class="py-2 text-right text-stone-600">${{ number_format((float) $linea->precio_unitario, 2) }}</td>
                                <td class="py-2 text-right font-medium text-stone-900">${{ number_format((float) $linea->importe, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="ml-auto w-full max-w-56 space-y-1 text-sm">
                    <div class="flex justify-between text-stone-600">
                        <span>Subtotal</span><span>${{ number_format((float) $detalle->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-stone-600">
                        <span>Descuento</span><span>−${{ number_format((float) $detalle->descuento, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-base font-bold text-stone-900">
                        <span>Total</span><span>${{ number_format((float) $detalle->total, 2) }}</span>
                    </div>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
