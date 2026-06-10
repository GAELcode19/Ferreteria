<div class="flex flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">Productos</flux:heading>
            <flux:text class="mt-1">Inventario y catálogo de la ferretería</flux:text>
        </div>
        @if (auth()->user()->isAdmin())
            <flux:button variant="primary" icon="plus" wire:click="crear">Nuevo producto</flux:button>
        @endif
    </div>

    {{-- Filtros --}}
    <div class="flex flex-wrap items-center gap-3">
        <div class="w-full max-w-xs">
            <flux:input icon="magnifying-glass" placeholder="Buscar por nombre o código…" wire:model.live.debounce.300ms="busqueda" clearable />
        </div>
        <div class="w-full max-w-56">
            <flux:select wire:model.live="filtroCategoria">
                <flux:select.option value="">Todas las categorías</flux:select.option>
                @foreach ($categorias as $categoria)
                    <flux:select.option value="{{ $categoria->id }}">{{ $categoria->nombre }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
        <flux:switch wire:model.live="soloStockBajo" label="Solo stock bajo" />
    </div>

    {{-- Tabla --}}
    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-100 bg-stone-50/60 text-left text-xs uppercase tracking-wide text-stone-500">
                        <th class="px-5 py-3 font-medium">Código</th>
                        <th class="px-5 py-3 font-medium">Producto</th>
                        <th class="px-5 py-3 font-medium">Categoría</th>
                        <th class="px-5 py-3 text-right font-medium">Precio venta</th>
                        <th class="px-5 py-3 text-center font-medium">Stock</th>
                        <th class="px-5 py-3 text-center font-medium">Estado</th>
                        @if (auth()->user()->isAdmin())
                            <th class="px-5 py-3 text-right font-medium">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productos as $producto)
                        <tr class="border-b border-stone-50 hover:bg-stone-50" wire:key="producto-{{ $producto->id }}">
                            <td class="px-5 py-3 font-mono text-xs text-stone-600">{{ $producto->codigo }}</td>
                            <td class="px-5 py-3">
                                <div class="font-medium text-stone-900">{{ $producto->nombre }}</div>
                                @if ($producto->descripcion)
                                    <div class="max-w-72 truncate text-xs text-stone-500">{{ $producto->descripcion }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-stone-600">{{ $producto->categoria?->nombre ?? '—' }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-stone-900">${{ number_format((float) $producto->precio_venta, 2) }}</td>
                            <td class="px-5 py-3 text-center">
                                <flux:badge size="sm" color="{{ $producto->stock === 0 ? 'red' : ($producto->tieneStockBajo() ? 'amber' : 'lime') }}">
                                    {{ $producto->stock }} {{ $producto->unidad }}{{ $producto->stock === 1 ? '' : 's' }}
                                </flux:badge>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <flux:badge size="sm" color="{{ $producto->activo ? 'emerald' : 'zinc' }}">{{ $producto->activo ? 'Activo' : 'Inactivo' }}</flux:badge>
                            </td>
                            @if (auth()->user()->isAdmin())
                                <td class="px-5 py-3 text-right">
                                    <div class="flex justify-end gap-1">
                                        <flux:button size="sm" variant="subtle" icon="pencil-square" wire:click="editar({{ $producto->id }})" tooltip="Editar" />
                                        <flux:button size="sm" variant="subtle" icon="trash" wire:click="eliminar({{ $producto->id }})" wire:confirm="¿Eliminar el producto «{{ $producto->nombre }}»?" tooltip="Eliminar" />
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-stone-400">No se encontraron productos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-stone-100 px-5 py-3">
            {{ $productos->links() }}
        </div>
    </div>

    {{-- Modal crear/editar --}}
    <flux:modal name="producto-form" class="w-full max-w-2xl">
        <form wire:submit="guardar" class="space-y-5">
            <flux:heading size="lg">{{ $editando ? 'Editar producto' : 'Nuevo producto' }}</flux:heading>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input label="Código" wire:model="codigo" placeholder="FER-0001" />
                <flux:input label="Nombre" wire:model="nombre" placeholder="Martillo de uña 16 oz" />
            </div>

            <flux:input label="Descripción" wire:model="descripcion" placeholder="Opcional" />

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:select label="Categoría" wire:model="categoria_id">
                    <flux:select.option value="">Sin categoría</flux:select.option>
                    @foreach ($categorias as $categoria)
                        <flux:select.option value="{{ $categoria->id }}">{{ $categoria->nombre }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select label="Proveedor" wire:model="proveedor_id">
                    <flux:select.option value="">Sin proveedor</flux:select.option>
                    @foreach ($proveedores as $proveedor)
                        <flux:select.option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input label="Precio de compra" wire:model="precio_compra" type="number" step="0.01" min="0" icon="currency-dollar" />
                <flux:input label="Precio de venta" wire:model="precio_venta" type="number" step="0.01" min="0" icon="currency-dollar" />
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <flux:input label="Stock actual" wire:model="stock" type="number" min="0" />
                <flux:input label="Stock mínimo" wire:model="stock_minimo" type="number" min="0" />
                <flux:select label="Unidad" wire:model="unidad">
                    @foreach (['pieza', 'caja', 'metro', 'kilo', 'litro', 'paquete', 'rollo', 'juego'] as $u)
                        <flux:select.option value="{{ $u }}">{{ ucfirst($u) }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <flux:switch wire:model="activo" label="Producto activo" />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ $editando ? 'Guardar cambios' : 'Crear producto' }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
