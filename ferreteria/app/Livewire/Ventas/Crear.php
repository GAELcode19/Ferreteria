<?php

namespace App\Livewire\Ventas;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Crear extends Component
{
    public string $busqueda = '';

    /** @var array<int, array{producto_id:int, codigo:string, nombre:string, precio:float, cantidad:int, stock:int, unidad:string}> */
    public array $carrito = [];

    public string $cliente_id = '';

    public string $metodo_pago = 'efectivo';

    public string $descuento = '0';

    public ?string $folioGuardado = null;

    public function agregarProducto(int $id): void
    {
        $producto = Producto::where('activo', true)->findOrFail($id);

        if (isset($this->carrito[$id])) {
            $this->incrementar($id);

            return;
        }

        if ($producto->stock < 1) {
            $this->addError('carrito', "«{$producto->nombre}» no tiene existencias.");

            return;
        }

        $this->carrito[$id] = [
            'producto_id' => $producto->id,
            'codigo' => $producto->codigo,
            'nombre' => $producto->nombre,
            'precio' => (float) $producto->precio_venta,
            'cantidad' => 1,
            'stock' => $producto->stock,
            'unidad' => $producto->unidad,
        ];

        $this->busqueda = '';
        $this->resetErrorBag('carrito');
    }

    public function incrementar(int $id): void
    {
        if (! isset($this->carrito[$id])) {
            return;
        }

        if ($this->carrito[$id]['cantidad'] >= $this->carrito[$id]['stock']) {
            $this->addError('carrito', "Solo hay {$this->carrito[$id]['stock']} en existencia de «{$this->carrito[$id]['nombre']}».");

            return;
        }

        $this->carrito[$id]['cantidad']++;
        $this->resetErrorBag('carrito');
    }

    public function decrementar(int $id): void
    {
        if (! isset($this->carrito[$id])) {
            return;
        }

        if ($this->carrito[$id]['cantidad'] <= 1) {
            $this->quitar($id);

            return;
        }

        $this->carrito[$id]['cantidad']--;
        $this->resetErrorBag('carrito');
    }

    public function quitar(int $id): void
    {
        unset($this->carrito[$id]);
        $this->resetErrorBag('carrito');
    }

    public function getSubtotalProperty(): float
    {
        return round(collect($this->carrito)->sum(fn ($item) => $item['precio'] * $item['cantidad']), 2);
    }

    public function getTotalProperty(): float
    {
        return max(0, round($this->subtotal - (float) ($this->descuento ?: 0), 2));
    }

    public function cobrar(): void
    {
        $this->resetErrorBag();

        if (empty($this->carrito)) {
            $this->addError('carrito', 'Agrega al menos un producto a la venta.');

            return;
        }

        $this->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'descuento' => 'nullable|numeric|min:0|lte:'.$this->subtotal,
        ], [], ['metodo_pago' => 'método de pago', 'descuento' => 'descuento']);

        try {
            $venta = $this->procesarVenta();
        } catch (\RuntimeException $e) {
            $this->addError('carrito', $e->getMessage());

            return;
        }

        $this->reset(['carrito', 'cliente_id', 'metodo_pago', 'descuento', 'busqueda']);
        $this->folioGuardado = $venta->folio;
    }

    private function procesarVenta(): Venta
    {
        return DB::transaction(function () {
            $productos = Producto::whereIn('id', array_keys($this->carrito))->lockForUpdate()->get()->keyBy('id');

            foreach ($this->carrito as $id => $item) {
                $producto = $productos[$id] ?? null;

                if (! $producto || $producto->stock < $item['cantidad']) {
                    throw new \RuntimeException("Stock insuficiente para «{$item['nombre']}». Disponible: ".($producto?->stock ?? 0));
                }
            }

            $venta = Venta::create([
                'folio' => Venta::generarFolio(),
                'user_id' => auth()->id(),
                'cliente_id' => $this->cliente_id ?: null,
                'subtotal' => $this->subtotal,
                'descuento' => (float) ($this->descuento ?: 0),
                'total' => $this->total,
                'metodo_pago' => $this->metodo_pago,
                'estado' => 'completada',
            ]);

            foreach ($this->carrito as $id => $item) {
                $venta->detalles()->create([
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'importe' => round($item['precio'] * $item['cantidad'], 2),
                ]);

                $productos[$id]->decrement('stock', $item['cantidad']);
            }

            return $venta;
        });

        $this->reset(['carrito', 'cliente_id', 'metodo_pago', 'descuento', 'busqueda']);
        $this->folioGuardado = $venta->folio;
    }

    public function nuevaVenta(): void
    {
        $this->folioGuardado = null;
    }

    public function render()
    {
        $resultados = collect();

        if (strlen($this->busqueda) >= 2) {
            $resultados = Producto::where('activo', true)
                ->where(fn ($q) => $q
                    ->where('nombre', 'ilike', "%{$this->busqueda}%")
                    ->orWhere('codigo', 'ilike', "%{$this->busqueda}%"))
                ->orderBy('nombre')
                ->take(8)
                ->get();
        }

        return view('livewire.ventas.crear', [
            'resultados' => $resultados,
            'clientes' => Cliente::orderBy('nombre')->get(),
        ])->title('Nueva venta');
    }
}
