<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::middleware(['auth'])->group(function () {
    // Acceso para todo el personal (admin y empleado)
    Route::get('dashboard', \App\Livewire\Dashboard::class)->name('dashboard');
    Route::get('productos', \App\Livewire\Productos\Index::class)->name('productos.index');
    Route::get('clientes', \App\Livewire\Clientes\Index::class)->name('clientes.index');
    Route::get('ventas', \App\Livewire\Ventas\Index::class)->name('ventas.index');
    Route::get('ventas/nueva', \App\Livewire\Ventas\Crear::class)->name('ventas.crear');

    // Solo administradores
    Route::middleware(['admin'])->group(function () {
        Route::get('categorias', \App\Livewire\Categorias\Index::class)->name('categorias.index');
        Route::get('proveedores', \App\Livewire\Proveedores\Index::class)->name('proveedores.index');
        Route::get('usuarios', \App\Livewire\Usuarios\Index::class)->name('usuarios.index');
        Route::get('reportes', \App\Livewire\Reportes\Index::class)->name('reportes.index');
    });

    // Configuración de cuenta
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
