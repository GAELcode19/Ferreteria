<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public ?User $editando = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $role = 'empleado';

    public bool $activo = true;

    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    public function crear(): void
    {
        $this->reset(['editando', 'name', 'email', 'password', 'role', 'activo']);
        $this->resetValidation();
        Flux::modal('usuario-form')->show();
    }

    public function editar(int $id): void
    {
        $usuario = User::findOrFail($id);
        $this->editando = $usuario;
        $this->name = $usuario->name;
        $this->email = $usuario->email;
        $this->password = '';
        $this->role = $usuario->role;
        $this->activo = $usuario->activo;
        $this->resetValidation();
        Flux::modal('usuario-form')->show();
    }

    public function guardar(): void
    {
        $datos = $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editando?->id)],
            'password' => $this->editando ? 'nullable|string|min:8' : 'required|string|min:8',
            'role' => 'required|in:admin,empleado',
            'activo' => 'boolean',
        ], [], ['name' => 'nombre', 'password' => 'contraseña', 'role' => 'rol']);

        if ($datos['password'] === null || $datos['password'] === '') {
            unset($datos['password']);
        }

        if ($this->editando) {
            // Evitar que el admin se quite su propio rol o se desactive
            if ($this->editando->id === auth()->id()) {
                $datos['role'] = 'admin';
                $datos['activo'] = true;
            }

            $this->editando->update($datos);
        } else {
            $datos['email_verified_at'] = now();
            User::create($datos);
        }

        Flux::modal('usuario-form')->close();
        $this->reset(['editando', 'name', 'email', 'password', 'role', 'activo']);
    }

    public function alternarActivo(int $id): void
    {
        $usuario = User::findOrFail($id);

        if ($usuario->id === auth()->id()) {
            return;
        }

        $usuario->update(['activo' => ! $usuario->activo]);
    }

    public function render()
    {
        $usuarios = User::withCount('ventas')
            ->when($this->busqueda, fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'ilike', "%{$this->busqueda}%")
                ->orWhere('email', 'ilike', "%{$this->busqueda}%")))
            ->orderBy('name')
            ->paginate(12);

        return view('livewire.usuarios.index', ['usuarios' => $usuarios])->title('Usuarios');
    }
}
