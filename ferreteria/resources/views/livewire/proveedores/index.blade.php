<div class="flex flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">Proveedores</flux:heading>
            <flux:text class="mt-1">Empresas que surten a la ferretería</flux:text>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="crear">Nuevo proveedor</flux:button>
    </div>

    <div class="w-full max-w-xs">
        <flux:input icon="magnifying-glass" placeholder="Buscar proveedor…" wire:model.live.debounce.300ms="busqueda" clearable />
    </div>

    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-100 bg-stone-50/60 text-left text-xs uppercase tracking-wide text-stone-500">
                        <th class="px-5 py-3 font-medium">Proveedor</th>
                        <th class="px-5 py-3 font-medium">Contacto</th>
                        <th class="px-5 py-3 font-medium">Teléfono</th>
                        <th class="px-5 py-3 font-medium">Correo</th>
                        <th class="px-5 py-3 text-center font-medium">Productos</th>
                        <th class="px-5 py-3 text-center font-medium">Estado</th>
                        <th class="px-5 py-3 text-right font-medium">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($proveedores as $proveedor)
                        <tr class="border-b border-stone-50 hover:bg-stone-50" wire:key="proveedor-{{ $proveedor->id }}">
                            <td class="px-5 py-3">
                                <div class="font-medium text-stone-900">{{ $proveedor->nombre }}</div>
                                @if ($proveedor->direccion)
                                    <div class="max-w-64 truncate text-xs text-stone-500">{{ $proveedor->direccion }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-stone-600">{{ $proveedor->contacto ?? '—' }}</td>
                            <td class="px-5 py-3 text-stone-600">{{ $proveedor->telefono ?? '—' }}</td>
                            <td class="px-5 py-3 text-stone-600">{{ $proveedor->email ?? '—' }}</td>
                            <td class="px-5 py-3 text-center">
                                <flux:badge size="sm" color="zinc">{{ $proveedor->productos_count }}</flux:badge>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <flux:badge size="sm" color="{{ $proveedor->activo ? 'emerald' : 'zinc' }}">{{ $proveedor->activo ? 'Activo' : 'Inactivo' }}</flux:badge>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <flux:button size="sm" variant="subtle" icon="pencil-square" wire:click="editar({{ $proveedor->id }})" tooltip="Editar" />
                                    <flux:button size="sm" variant="subtle" icon="trash" wire:click="eliminar({{ $proveedor->id }})" wire:confirm="¿Eliminar al proveedor «{{ $proveedor->nombre }}»?" tooltip="Eliminar" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-stone-400">No hay proveedores registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-stone-100 px-5 py-3">
            {{ $proveedores->links() }}
        </div>
    </div>

    <flux:modal name="proveedor-form" class="w-full max-w-lg">
        <form wire:submit="guardar" class="space-y-5">
            <flux:heading size="lg">{{ $editando ? 'Editar proveedor' : 'Nuevo proveedor' }}</flux:heading>
            <flux:input label="Nombre / Razón social" wire:model="nombre" placeholder="Truper S.A. de C.V." />
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input label="Persona de contacto" wire:model="contacto" placeholder="Opcional" />
                <flux:input label="Teléfono" wire:model="telefono" placeholder="Opcional" />
            </div>
            <flux:input label="Correo electrónico" wire:model="email" type="email" placeholder="Opcional" />
            <flux:input label="Dirección" wire:model="direccion" placeholder="Opcional" />
            <flux:switch wire:model="activo" label="Proveedor activo" />
            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ $editando ? 'Guardar cambios' : 'Crear proveedor' }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
