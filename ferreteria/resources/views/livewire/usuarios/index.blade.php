<div class="flex flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">Usuarios</flux:heading>
            <flux:text class="mt-1">Personal con acceso al sistema</flux:text>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="crear">Nuevo usuario</flux:button>
    </div>

    <div class="w-full max-w-xs">
        <flux:input icon="magnifying-glass" placeholder="Buscar usuario…" wire:model.live.debounce.300ms="busqueda" clearable />
    </div>

    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-100 bg-stone-50/60 text-left text-xs uppercase tracking-wide text-stone-500">
                        <th class="px-5 py-3 font-medium">Usuario</th>
                        <th class="px-5 py-3 font-medium">Correo</th>
                        <th class="px-5 py-3 text-center font-medium">Rol</th>
                        <th class="px-5 py-3 text-center font-medium">Ventas</th>
                        <th class="px-5 py-3 text-center font-medium">Estado</th>
                        <th class="px-5 py-3 text-right font-medium">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $usuario)
                        <tr class="border-b border-stone-50 hover:bg-stone-50" wire:key="usuario-{{ $usuario->id }}">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-xs font-semibold text-amber-800">
                                        {{ $usuario->initials() }}
                                    </span>
                                    <span class="font-medium text-stone-900">{{ $usuario->name }}</span>
                                    @if ($usuario->id === auth()->id())
                                        <flux:badge size="sm" color="sky">Tú</flux:badge>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3 text-stone-600">{{ $usuario->email }}</td>
                            <td class="px-5 py-3 text-center">
                                <flux:badge size="sm" color="{{ $usuario->role === 'admin' ? 'purple' : 'zinc' }}">
                                    {{ $usuario->role === 'admin' ? 'Administrador' : 'Empleado' }}
                                </flux:badge>
                            </td>
                            <td class="px-5 py-3 text-center text-stone-600">{{ $usuario->ventas_count }}</td>
                            <td class="px-5 py-3 text-center">
                                <flux:badge size="sm" color="{{ $usuario->activo ? 'emerald' : 'red' }}">{{ $usuario->activo ? 'Activo' : 'Inactivo' }}</flux:badge>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <flux:button size="sm" variant="subtle" icon="pencil-square" wire:click="editar({{ $usuario->id }})" tooltip="Editar" />
                                    @if ($usuario->id !== auth()->id())
                                        <flux:button size="sm" variant="subtle" icon="{{ $usuario->activo ? 'lock-closed' : 'lock-open' }}"
                                            wire:click="alternarActivo({{ $usuario->id }})"
                                            wire:confirm="¿{{ $usuario->activo ? 'Desactivar' : 'Activar' }} a {{ $usuario->name }}?"
                                            tooltip="{{ $usuario->activo ? 'Desactivar acceso' : 'Activar acceso' }}" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-stone-400">No hay usuarios.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-stone-100 px-5 py-3">
            {{ $usuarios->links() }}
        </div>
    </div>

    <flux:modal name="usuario-form" class="w-full max-w-lg">
        <form wire:submit="guardar" class="space-y-5">
            <flux:heading size="lg">{{ $editando ? 'Editar usuario' : 'Nuevo usuario' }}</flux:heading>
            <flux:input label="Nombre completo" wire:model="name" />
            <flux:input label="Correo electrónico" wire:model="email" type="email" />
            <flux:input label="{{ $editando ? 'Nueva contraseña (dejar en blanco para no cambiar)' : 'Contraseña' }}" wire:model="password" type="password" viewable />
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:select label="Rol" wire:model="role" :disabled="$editando?->id === auth()->id()">
                    <flux:select.option value="empleado">Empleado</flux:select.option>
                    <flux:select.option value="admin">Administrador</flux:select.option>
                </flux:select>
                <div class="flex items-end pb-2">
                    <flux:switch wire:model="activo" label="Acceso activo" :disabled="$editando?->id === auth()->id()" />
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ $editando ? 'Guardar cambios' : 'Crear usuario' }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
