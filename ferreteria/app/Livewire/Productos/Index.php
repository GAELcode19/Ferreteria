<?php

namespace App\Livewire\Productos;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public string $filtroCategoria = '';

    public bool $soloStockBajo = false;

    public ?Producto $editando = null;

    public string $codigo = '';

    public string $nombre = '';

    public string $descripcion = '';

    public string $categoria_id = '';

    public string $proveedor_id = '';

    public string $precio_compra = '';

    public string $precio_venta = '';

    public string $stock = '0';

    public string $stock_minimo = '5';

    public string $unidad = 'pieza';

    public bool $activo = true;

    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatedFiltroCategoria(): void
    {
        $this->resetPage();
    }

    public function updatedSoloStockBajo(): void
    {
        $this->resetPage();
    }

    public function crear(): void
    {
        $this->autorizar();
        $this->limpiarFormulario();
        Flux::modal('producto-form')->show();
    }

    public function editar(int $id): void
    {
        $this->autorizar();
        $producto = Producto::findOrFail($id);
        $this->editando = $producto;
        $this->codigo = $producto->codigo;
        $this->nombre = $producto->nombre;
        $this->descripcion = $producto->descripcion ?? '';
        $this->categoria_id = (string) ($producto->categoria_id ?? '');
        $this->proveedor_id = (string) ($producto->proveedor_id ?? '');
        $this->precio_compra = (string) $producto->precio_compra;
        $this->precio_venta = (string) $producto->precio_venta;
        $this->stock = (string) $producto->stock;
        $this->stock_minimo = (string) $producto->stock_minimo;
        $this->unidad = $producto->unidad;
        $this->activo = $producto->activo;
        Flux::modal('producto-form')->show();
    }

    public function guardar(): void
    {
        $this->autorizar();

        $datos = $this->validate([
            'codigo' => 'required|string|max:50|unique:productos,codigo'.($this->editando ? ','.$this->editando->id : ''),
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            'categoria_id' => 'nullable|exists:categorias,id',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'unidad' => 'required|string|max:20',
            'activo' => 'boolean',
        ], [], [
            'codigo' => 'código',
            'categoria_id' => 'categoría',
            'proveedor_id' => 'proveedor',
            'precio_compra' => 'precio de compra',
            'precio_venta' => 'precio de venta',
            'stock_minimo' => 'stock mínimo',
        ]);

        $datos['categoria_id'] = $datos['categoria_id'] ?: null;
        $datos['proveedor_id'] = $datos['proveedor_id'] ?: null;

        if ($this->editando) {
            $this->editando->update($datos);
        } else {
            Producto::create($datos);
        }

        Flux::modal('producto-form')->close();
        $this->limpiarFormulario();
    }

    public function eliminar(int $id): void
    {
        $this->autorizar();
        $producto = Producto::findOrFail($id);

        if (\App\Models\VentaDetalle::where('producto_id', $producto->id)->exists()) {
            $producto->update(['activo' => false]);
        } else {
            $producto->delete();
        }
    }

    public function render()
    {
        $productos = Producto::with(['categoria', 'proveedor'])
            ->when($this->busqueda, fn ($q) => $q->where(fn ($q) => $q
                ->where('nombre', 'ilike', "%{$this->busqueda}%")
                ->orWhere('codigo', 'ilike', "%{$this->busqueda}%")))
            ->when($this->filtroCategoria, fn ($q) => $q->where('categoria_id', $this->filtroCategoria))
            ->when($this->soloStockBajo, fn ($q) => $q->stockBajo())
            ->orderBy('nombre')
            ->paginate(12);

        return view('livewire.productos.index', [
            'productos' => $productos,
            'categorias' => Categoria::orderBy('nombre')->get(),
            'proveedores' => Proveedor::where('activo', true)->orderBy('nombre')->get(),
        ])->title('Productos');
    }

    private function autorizar(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
    }

    private function limpiarFormulario(): void
    {
        $this->reset([
            'editando', 'codigo', 'nombre', 'descripcion', 'categoria_id', 'proveedor_id',
            'precio_compra', 'precio_venta', 'stock', 'stock_minimo', 'unidad', 'activo',
        ]);
        $this->resetValidation();
    }
}
