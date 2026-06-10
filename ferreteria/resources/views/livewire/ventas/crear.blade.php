<div class="flex flex-col gap-6">
    <div>
        <flux:heading size="xl">Nueva venta</flux:heading>
        <flux:text class="mt-1">Punto de venta — agrega productos y cobra</flux:text>
    </div>

    @if ($folioGuardado)
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-6 text-center">
            <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-emerald-100">
                <flux:icon.check-circle class="size-8 text-emerald-600" />
            </div>
            <flux:heading size="lg" class="mt-3">Venta registrada con éxito</flux:heading>
            <flux:text class="mt-1">Folio <span class="font-mono font-semibold">{{ $folioGuardado }}</span></flux:text>
            <div class="mt-4 flex justify-center gap-2">
                <flux:button variant="primary" icon="plus" wire:click="nuevaVenta">Nueva venta</flux:button>
                <flux:button variant="ghost" :href="route('ventas.index')" wire:navigate>Ver historial</flux:button>
            </div>
        </div>
    @else
        <div class="grid gap-6 xl:grid-cols-5">
            {{-- Búsqueda y carrito --}}
            <div class="flex flex-col gap-4 xl:col-span-3">
                <div class="relative">
                    <flux:input icon="magnifying-glass" placeholder="Busca un producto por nombre o código (mín. 2 letras)…" wire:model.live.debounce.250ms="busqueda" clearable autofocus />

                    @if ($resultados->isNotEmpty())
                        <div class="absolute z-20 mt-2 w-full overflow-hidden rounded-xl border border-stone-200 bg-white shadow-lg">
                            @foreach ($resultados as $producto)
                                <button type="button" wire:click="agregarProducto({{ $producto->id }})"
                                    class="flex w-full items-center justify-between gap-3 border-b border-stone-50 px-4 py-3 text-left hover:bg-amber-50 {{ $producto->stock < 1 ? 'opacity-50' : '' }}">
                                    <div class="min-w-0">
                                        <div class="truncate font-medium text-stone-900">{{ $producto->nombre }}</div>
                                        <div class="text-xs text-stone-500">{{ $producto->codigo }} · {{ $producto->stock }} en existencia</div>
                                    </div>
                                    <div class="shrink-0 font-semibold text-stone-900">${{ number_format((float) $producto->precio_venta, 2) }}</div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                @error('carrito')
                    <flux:callout variant="danger" icon="exclamation-triangle">{{ $message }}</flux:callout>
                @enderror

                <div class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-stone-100 bg-stone-50/60 text-left text-xs uppercase tracking-wide text-stone-500">
                                <th class="px-4 py-3 font-medium">Producto</th>
                                <th class="px-4 py-3 text-center font-medium">Cantidad</th>
                                <th class="px-4 py-3 text-right font-medium">Precio</th>
                                <th class="px-4 py-3 text-right font-medium">Importe</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($carrito as $item)
                                <tr class="border-b border-stone-50" wire:key="carrito-{{ $item['producto_id'] }}">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-stone-900">{{ $item['nombre'] }}</div>
                                        <div class="text-xs text-stone-500">{{ $item['codigo'] }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-1">
                                            <flux:button size="xs" variant="subtle" icon="minus" wire:click="decrementar({{ $item['producto_id'] }})" />
                                            <span class="w-10 text-center font-semibold">{{ $item['cantidad'] }}</span>
                                            <flux:button size="xs" variant="subtle" icon="plus" wire:click="incrementar({{ $item['producto_id'] }})" />
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right text-stone-600">${{ number_format($item['precio'], 2) }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-stone-900">${{ number_format($item['precio'] * $item['cantidad'], 2) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <flux:button size="xs" variant="subtle" icon="x-mark" wire:click="quitar({{ $item['producto_id'] }})" tooltip="Quitar" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-14 text-center text-stone-400">
                                        <flux:icon.shopping-cart class="mx-auto mb-2 size-8 text-stone-300" />
                                        El carrito está vacío. Busca un producto arriba para empezar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Resumen de cobro --}}
            <div class="xl:col-span-2">
                <div class="sticky top-6 flex flex-col gap-4 rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
                    <flux:heading size="lg">Resumen</flux:heading>

                    <flux:select label="Cliente" wire:model="cliente_id">
                        <flux:select.option value="">Público general</flux:select.option>
                        @foreach ($clientes as $cliente)
                            <flux:select.option value="{{ $cliente->id }}">{{ $cliente->nombre }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select label="Método de pago" wire:model="metodo_pago">
                        <flux:select.option value="efectivo">Efectivo</flux:select.option>
                        <flux:select.option value="tarjeta">Tarjeta</flux:select.option>
                        <flux:select.option value="transferencia">Transferencia</flux:select.option>
                    </flux:select>

                    <flux:input label="Descuento ($)" wire:model.live.debounce.300ms="descuento" type="number" step="0.01" min="0" />

                    <flux:separator />

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-stone-600">
                            <span>Subtotal</span>
                            <span>${{ number_format($this->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-stone-600">
                            <span>Descuento</span>
                            <span>−${{ number_format((float) ($descuento ?: 0), 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-stone-900">
                            <span>Total</span>
                            <span>${{ number_format($this->total, 2) }}</span>
                        </div>
                    </div>

                    <flux:button variant="primary" icon="banknotes" wire:click="cobrar" class="w-full" :disabled="empty($carrito)">
                        Cobrar ${{ number_format($this->total, 2) }}
                    </flux:button>
                </div>
            </div>
        </div>
    @endif
</div>
