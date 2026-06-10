<div class="flex flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">Categorías</flux:heading>
            <flux:text class="mt-1">Organiza el catálogo de productos</flux:text>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="crear">Nueva categoría</flux:button>
    </div>

    <div class="w-full max-w-xs">
        <flux:input icon="magnifying-glass" placeholder="Buscar categoría…" wire:model.live.debounce.300ms="busqueda" clearable />
    </div>

    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-stone-100 bg-stone-50/60 text-left text-xs uppercase tracking-wide text-stone-500">
                    <th class="px-5 py-3 font-medium">Nombre</th>
                    <th class="px-5 py-3 font-medium">Descripción</th>
                    <th class="px-5 py-3 text-center font-medium">Productos</th>
                    <th class="px-5 py-3 text-right font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categorias as $categoria)
                    <tr class="border-b border-stone-50 hover:bg-stone-50" wire:key="categoria-{{ $categoria->id }}">
                        <td class="px-5 py-3 font-medium text-stone-900">{{ $categoria->nombre }}</td>
                        <td class="px-5 py-3 text-stone-600">{{ $categoria->descripcion ?? '—' }}</td>
                        <td class="px-5 py-3 text-center">
                            <flux:badge size="sm" color="zinc">{{ $categoria->productos_count }}</flux:badge>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex justify-end gap-1">
                                <flux:button size="sm" variant="subtle" icon="pencil-square" wire:click="editar({{ $categoria->id }})" tooltip="Editar" />
                                <flux:button size="sm" variant="subtle" icon="trash" wire:click="eliminar({{ $categoria->id }})" wire:confirm="¿Eliminar la categoría «{{ $categoria->nombre }}»? Los productos quedarán sin categoría." tooltip="Eliminar" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-stone-400">No hay categorías registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-stone-100 px-5 py-3">
            {{ $categorias->links() }}
        </div>
    </div>

    <flux:modal name="categoria-form" class="w-full max-w-md">
        <form wire:submit="guardar" class="space-y-5">
            <flux:heading size="lg">{{ $editando ? 'Editar categoría' : 'Nueva categoría' }}</flux:heading>
            <flux:input label="Nombre" wire:model="nombre" placeholder="Herramientas manuales" />
            <flux:input label="Descripción" wire:model="descripcion" placeholder="Opcional" />
            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ $editando ? 'Guardar cambios' : 'Crear categoría' }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
