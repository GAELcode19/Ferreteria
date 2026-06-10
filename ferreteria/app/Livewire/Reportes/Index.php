<?php

namespace App\Livewire\Reportes;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public string $desde = '';

    public string $hasta = '';

    public function mount(): void
    {
        $this->desde = now()->startOfMonth()->toDateString();
        $this->hasta = now()->toDateString();
    }

    public function render()
    {
        $inicio = $this->desde ? \Carbon\Carbon::parse($this->desde)->startOfDay() : now()->startOfMonth();
        $fin = $this->hasta ? \Carbon\Carbon::parse($this->hasta)->endOfDay() : now()->endOfDay();

        $ventas = Venta::where('ventas.estado', 'completada')->whereBetween('ventas.created_at', [$inicio, $fin]);

        $totalVendido = (clone $ventas)->sum('total');
        $numVentas = (clone $ventas)->count();
        $ticketPromedio = $numVentas > 0 ? $totalVendido / $numVentas : 0;

        $porMetodo = (clone $ventas)
            ->select('metodo_pago', DB::raw('count(*) as cantidad'), DB::raw('sum(total) as total'))
            ->groupBy('metodo_pago')
            ->get();

        $masVendidos = VentaDetalle::query()
            ->join('ventas', 'ventas.id', '=', 'venta_detalles.venta_id')
            ->join('productos', 'productos.id', '=', 'venta_detalles.producto_id')
            ->where('ventas.estado', 'completada')
            ->whereBetween('ventas.created_at', [$inicio, $fin])
            ->select('productos.nombre', 'productos.codigo', DB::raw('sum(venta_detalles.cantidad) as unidades'), DB::raw('sum(venta_detalles.importe) as importe'))
            ->groupBy('productos.id', 'productos.nombre', 'productos.codigo')
            ->orderByDesc(DB::raw('sum(venta_detalles.cantidad)'))
            ->take(10)
            ->get();

        $porVendedor = (clone $ventas)
            ->join('users', 'users.id', '=', 'ventas.user_id')
            ->select('users.name', DB::raw('count(*) as cantidad'), DB::raw('sum(ventas.total) as total'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc(DB::raw('sum(ventas.total)'))
            ->get();

        $valorInventario = Producto::where('activo', true)->sum(DB::raw('stock * precio_compra'));

        return view('livewire.reportes.index', [
            'totalVendido' => $totalVendido,
            'numVentas' => $numVentas,
            'ticketPromedio' => $ticketPromedio,
            'porMetodo' => $porMetodo,
            'masVendidos' => $masVendidos,
            'porVendedor' => $porVendedor,
            'valorInventario' => $valorInventario,
        ])->title('Reportes');
    }
}
