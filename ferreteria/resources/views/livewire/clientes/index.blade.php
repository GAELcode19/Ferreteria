<div class="flex flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">Clientes</flux:heading>
            <flux:text class="mt-1">Cartera de clientes de la ferretería</flux:text>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="crear">Nuevo cliente</flux:button>
    </div>

    <div class="w-full max-w-xs">
        <flux:input icon="magnifying-glass" placeholder="Buscar por nombre, teléfono o RFC…" wire:model.live.debounce.300ms="busqueda" clearable />
    </div>

    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-100 bg-stone-50/60 text-left text-xs uppercase tracking-wide text-stone-500">
                        <th class="px-5 py-3 font-medium">Cliente</th>
                        <th class="px-5 py-3 font-medium">Teléfono</th>
                        <th class="px-5 py-3 font-medium">Correo</th>
                        <th class="px-5 py-3 font-medium">RFC</th>
                        <th class="px-5 py-3 text-center font-medium">Compras</th>
                        <th class="px-5 py-3 text-right font-medium">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clientes as $cliente)
                        <tr class="border-b border-stone-50 hover:bg-stone-50" wire:key="cliente-{{ $cliente->id }}">
                            <td class="px-5 py-3">
                                <div class="font-medium text-stone-900">{{ $cliente->nombre }}</div>
                                @if ($cliente->direccion)
                                    <div class="max-w-64 truncate text-xs text-stone-500">{{ $cliente->direccion }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-stone-600">{{ $cliente->telefono ?? '—' }}</td>
                            <td class="px-5 py-3 text-stone-600">{{ $cliente->email ?? '—' }}</td>
                            <td class="px-5 py-3 font-mono text-xs text-stone-600">{{ $cliente->rfc ?? '—' }}</td>
                            <td class="px-5 py-3 text-center">
                                <flux:badge size="sm" color="zinc">{{ $cliente->ventas_count }}</flux:badge>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <flux:button size="sm" variant="subtle" icon="pencil-square" wire:click="editar({{ $cliente->id }})" tooltip="Editar" />
                                    @if (auth()->user()->isAdmin() && $cliente->ventas_count === 0)
                                        <flux:button size="sm" variant="subtle" icon="trash" wire:click="eliminar({{ $cliente->id }})" wire:confirm="¿Eliminar al cliente «{{ $cliente->nombre }}»?" tooltip="Eliminar" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-stone-400">No hay clientes registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-stone-100 px-5 py-3">
            {{ $clientes->links() }}
        </div>
    </div>

    <flux:modal name="cliente-form" class="w-full max-w-lg">
        <form wire:submit="guardar" class="space-y-5">
            <flux:heading size="lg">{{ $editando ? 'Editar cliente' : 'Nuevo cliente' }}</flux:heading>
            <flux:input label="Nombre completo" wire:model="nombre" placeholder="Juan Pérez García" />
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input label="Teléfono" wire:model="telefono" placeholder="Opcional" />
                <flux:input label="RFC" wire:model="rfc" placeholder="Opcional" />
            </div>
            <flux:input label="Correo electrónico" wire:model="email" type="email" placeholder="Opcional" />
            <flux:input label="Dirección" wire:model="direccion" placeholder="Opcional" />
            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ $editando ? 'Guardar cambios' : 'Crear cliente' }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
