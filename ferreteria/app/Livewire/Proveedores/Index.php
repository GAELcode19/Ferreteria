<?php

namespace App\Livewire\Proveedores;

use App\Models\Proveedor;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public ?Proveedor $editando = null;

    public string $nombre = '';

    public string $contacto = '';

    public string $telefono = '';

    public string $email = '';

    public string $direccion = '';

    public bool $activo = true;

    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    public function crear(): void
    {
        $this->reset(['editando', 'nombre', 'contacto', 'telefono', 'email', 'direccion', 'activo']);
        $this->resetValidation();
        Flux::modal('proveedor-form')->show();
    }

    public function editar(int $id): void
    {
        $proveedor = Proveedor::findOrFail($id);
        $this->editando = $proveedor;
        $this->nombre = $proveedor->nombre;
        $this->contacto = $proveedor->contacto ?? '';
        $this->telefono = $proveedor->telefono ?? '';
        $this->email = $proveedor->email ?? '';
        $this->direccion = $proveedor->direccion ?? '';
        $this->activo = $proveedor->activo;
        $this->resetValidation();
        Flux::modal('proveedor-form')->show();
    }

    public function guardar(): void
    {
        $datos = $this->validate([
            'nombre' => 'required|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:255',
            'activo' => 'boolean',
        ], [], ['direccion' => 'dirección', 'telefono' => 'teléfono']);

        if ($this->editando) {
            $this->editando->update($datos);
        } else {
            Proveedor::create($datos);
        }

        Flux::modal('proveedor-form')->close();
        $this->reset(['editando', 'nombre', 'contacto', 'telefono', 'email', 'direccion', 'activo']);
    }

    public function eliminar(int $id): void
    {
        $proveedor = Proveedor::findOrFail($id);

        if ($proveedor->productos()->exists()) {
            $proveedor->update(['activo' => false]);
        } else {
            $proveedor->delete();
        }
    }

    public function render()
    {
        $proveedores = Proveedor::withCount('productos')
            ->when($this->busqueda, fn ($q) => $q->where(fn ($q) => $q
                ->where('nombre', 'ilike', "%{$this->busqueda}%")
                ->orWhere('contacto', 'ilike', "%{$this->busqueda}%")))
            ->orderBy('nombre')
            ->paginate(12);

        return view('livewire.proveedores.index', ['proveedores' => $proveedores])->title('Proveedores');
    }
}
