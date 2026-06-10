<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public ?Cliente $editando = null;

    public string $nombre = '';

    public string $telefono = '';

    public string $email = '';

    public string $direccion = '';

    public string $rfc = '';

    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    public function crear(): void
    {
        $this->reset(['editando', 'nombre', 'telefono', 'email', 'direccion', 'rfc']);
        $this->resetValidation();
        Flux::modal('cliente-form')->show();
    }

    public function editar(int $id): void
    {
        $cliente = Cliente::findOrFail($id);
        $this->editando = $cliente;
        $this->nombre = $cliente->nombre;
        $this->telefono = $cliente->telefono ?? '';
        $this->email = $cliente->email ?? '';
        $this->direccion = $cliente->direccion ?? '';
        $this->rfc = $cliente->rfc ?? '';
        $this->resetValidation();
        Flux::modal('cliente-form')->show();
    }

    public function guardar(): void
    {
        $datos = $this->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:255',
            'rfc' => 'nullable|string|max:20',
        ], [], ['direccion' => 'dirección', 'telefono' => 'teléfono', 'rfc' => 'RFC']);

        if ($this->editando) {
            $this->editando->update($datos);
        } else {
            Cliente::create($datos);
        }

        Flux::modal('cliente-form')->close();
        $this->reset(['editando', 'nombre', 'telefono', 'email', 'direccion', 'rfc']);
    }

    public function eliminar(int $id): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $cliente = Cliente::findOrFail($id);

        if (! $cliente->ventas()->exists()) {
            $cliente->delete();
        }
    }

    public function render()
    {
        $clientes = Cliente::withCount('ventas')
            ->when($this->busqueda, fn ($q) => $q->where(fn ($q) => $q
                ->where('nombre', 'ilike', "%{$this->busqueda}%")
                ->orWhere('telefono', 'ilike', "%{$this->busqueda}%")
                ->orWhere('rfc', 'ilike', "%{$this->busqueda}%")))
            ->orderBy('nombre')
            ->paginate(12);

        return view('livewire.clientes.index', ['clientes' => $clientes])->title('Clientes');
    }
}
