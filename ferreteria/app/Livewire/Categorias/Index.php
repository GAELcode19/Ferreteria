<?php

namespace App\Livewire\Categorias;

use App\Models\Categoria;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public ?Categoria $editando = null;

    public string $nombre = '';

    public string $descripcion = '';

    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    public function crear(): void
    {
        $this->reset(['editando', 'nombre', 'descripcion']);
        $this->resetValidation();
        Flux::modal('categoria-form')->show();
    }

    public function editar(int $id): void
    {
        $categoria = Categoria::findOrFail($id);
        $this->editando = $categoria;
        $this->nombre = $categoria->nombre;
        $this->descripcion = $categoria->descripcion ?? '';
        $this->resetValidation();
        Flux::modal('categoria-form')->show();
    }

    public function guardar(): void
    {
        $datos = $this->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre'.($this->editando ? ','.$this->editando->id : ''),
            'descripcion' => 'nullable|string|max:255',
        ], [], ['descripcion' => 'descripción']);

        if ($this->editando) {
            $this->editando->update($datos);
        } else {
            Categoria::create($datos);
        }

        Flux::modal('categoria-form')->close();
        $this->reset(['editando', 'nombre', 'descripcion']);
    }

    public function eliminar(int $id): void
    {
        Categoria::findOrFail($id)->delete();
    }

    public function render()
    {
        $categorias = Categoria::withCount('productos')
            ->when($this->busqueda, fn ($q) => $q->where('nombre', 'ilike', "%{$this->busqueda}%"))
            ->orderBy('nombre')
            ->paginate(12);

        return view('livewire.categorias.index', ['categorias' => $categorias])->title('Categorías');
    }
}
