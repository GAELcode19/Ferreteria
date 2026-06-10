<?php

namespace App\Livewire\Ventas;

use App\Models\Venta;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public string $fecha = '';

    public ?Venta $detalle = null;

    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatedFecha(): void
    {
        $this->resetPage();
    }

    public function verDetalle(int $id): void
    {
        $this->detalle = Venta::with(['detalles.producto', 'cliente', 'usuario'])->findOrFail($id);
        Flux::modal('venta-detalle')->show();
    }

    public function anular(int $id): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $venta = Venta::with('detalles')->findOrFail($id);

        if ($venta->estado === 'anulada') {
            return;
        }

        DB::transaction(function () use ($venta) {
            foreach ($venta->detalles as $detalle) {
                $detalle->producto()->lockForUpdate()->first()?->increment('stock', $detalle->cantidad);
            }

            $venta->update(['estado' => 'anulada']);
        });

        if ($this->detalle?->id === $venta->id) {
            $this->detalle = $venta->fresh(['detalles.producto', 'cliente', 'usuario']);
        }
    }

    public function render()
    {
        $ventas = Venta::with(['cliente', 'usuario'])
            ->when($this->busqueda, fn ($q) => $q->where(fn ($q) => $q
                ->where('folio', 'ilike', "%{$this->busqueda}%")
                ->orWhereHas('cliente', fn ($q) => $q->where('nombre', 'ilike', "%{$this->busqueda}%"))))
            ->when($this->fecha, fn ($q) => $q->whereDate('created_at', $this->fecha))
            ->latest()
            ->paginate(15);

        return view('livewire.ventas.index', ['ventas' => $ventas])->title('Ventas');
    }
}
