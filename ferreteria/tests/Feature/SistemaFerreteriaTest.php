<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('la raíz redirige al login para invitados', function () {
    $this->get('/')->assertRedirect(route('login'));
});

test('el login renderiza', function () {
    $this->get('/login')->assertOk();
});

test('el registro público está deshabilitado', function () {
    $this->get('/register')->assertNotFound();
});

test('el admin puede ver todas las secciones', function () {
    $admin = User::factory()->create(['role' => 'admin', 'activo' => true]);

    foreach (['dashboard', 'productos', 'clientes', 'ventas', 'ventas/nueva', 'categorias', 'proveedores', 'usuarios', 'reportes'] as $ruta) {
        $this->actingAs($admin)->get('/'.$ruta)->assertOk();
    }
});

test('el empleado no puede ver secciones de administración', function () {
    $empleado = User::factory()->create(['role' => 'empleado', 'activo' => true]);

    foreach (['dashboard', 'productos', 'clientes', 'ventas', 'ventas/nueva'] as $ruta) {
        $this->actingAs($empleado)->get('/'.$ruta)->assertOk();
    }

    foreach (['categorias', 'proveedores', 'usuarios', 'reportes'] as $ruta) {
        $this->actingAs($empleado)->get('/'.$ruta)->assertForbidden();
    }
});
