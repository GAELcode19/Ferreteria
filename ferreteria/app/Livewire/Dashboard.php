<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $hoy = now()->startOfDay();

        return view('livewire.dashboard', [
            'ventasHoy' => Venta::where('estado', 'completada')->where('created_at', '>=', $hoy)->sum('total'),
            'numVentasHoy' => Venta::where('estado', 'completada')->where('created_at', '>=', $hoy)->count(),
            'ventasMes' => Venta::where('estado', 'completada')->where('created_at', '>=', now()->startOfMonth())->sum('total'),
            'totalProductos' => Producto::where('activo', true)->count(),
            'stockBajo' => Producto::where('activo', true)->stockBajo()->count(),
            'totalClientes' => Cliente::count(),
            'ultimasVentas' => Venta::with(['cliente', 'usuario'])->latest()->take(8)->get(),
            'productosStockBajo' => Producto::where('activo', true)->stockBajo()->orderBy('stock')->take(8)->get(),
        ])->title('Panel principal');
    }
}
