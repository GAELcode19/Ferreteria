<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuarios del sistema
        User::updateOrCreate(
            ['email' => 'admin@ferreteria.com'],
            [
                'name' => 'Gael Guzmán',
                'password' => 'admin1234',
                'role' => 'admin',
                'activo' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'empleado@ferreteria.com'],
            [
                'name' => 'Empleado Demo',
                'password' => 'empleado1234',
                'role' => 'empleado',
                'activo' => true,
                'email_verified_at' => now(),
            ]
        );

        // Categorías típicas de una ferretería
        $categorias = collect([
            'Herramientas manuales' => 'Martillos, desarmadores, pinzas, llaves',
            'Herramientas eléctricas' => 'Taladros, esmeriles, sierras',
            'Tornillería y fijación' => 'Tornillos, clavos, taquetes, anclas',
            'Plomería' => 'Tubería, conexiones, llaves de paso',
            'Electricidad' => 'Cable, contactos, apagadores, focos',
            'Pinturas y solventes' => 'Pintura, brochas, rodillos, thinner',
            'Construcción' => 'Cemento, yeso, varilla, alambre',
            'Jardinería' => 'Mangueras, palas, rastrillos',
        ])->map(fn ($descripcion, $nombre) => Categoria::updateOrCreate(
            ['nombre' => $nombre],
            ['descripcion' => $descripcion]
        ));

        // Proveedores de ejemplo
        $proveedores = collect([
            ['nombre' => 'Truper S.A. de C.V.', 'contacto' => 'Carlos Mendoza', 'telefono' => '55-5728-9100', 'email' => 'ventas@truper.com'],
            ['nombre' => 'Fierros y Aceros del Norte', 'contacto' => 'Ana Robles', 'telefono' => '81-8345-2200', 'email' => 'contacto@fianorte.mx'],
            ['nombre' => 'Distribuidora Eléctrica Volta', 'contacto' => 'Luis Herrera', 'telefono' => '33-3615-7788', 'email' => 'pedidos@volta.mx'],
        ])->map(fn ($p) => Proveedor::updateOrCreate(['nombre' => $p['nombre']], $p + ['activo' => true]))->values();

        // Productos de ejemplo
        $productos = [
            ['codigo' => 'HM-0001', 'nombre' => 'Martillo de uña 16 oz', 'categoria' => 'Herramientas manuales', 'proveedor' => 0, 'compra' => 95.00, 'venta' => 149.00, 'stock' => 24, 'minimo' => 5, 'unidad' => 'pieza'],
            ['codigo' => 'HM-0002', 'nombre' => 'Juego de desarmadores 6 pzas', 'categoria' => 'Herramientas manuales', 'proveedor' => 0, 'compra' => 120.00, 'venta' => 189.00, 'stock' => 15, 'minimo' => 4, 'unidad' => 'juego'],
            ['codigo' => 'HM-0003', 'nombre' => 'Pinza de presión 10"', 'categoria' => 'Herramientas manuales', 'proveedor' => 0, 'compra' => 85.00, 'venta' => 135.00, 'stock' => 18, 'minimo' => 5, 'unidad' => 'pieza'],
            ['codigo' => 'HE-0001', 'nombre' => 'Taladro percutor 1/2" 650W', 'categoria' => 'Herramientas eléctricas', 'proveedor' => 0, 'compra' => 780.00, 'venta' => 1190.00, 'stock' => 8, 'minimo' => 2, 'unidad' => 'pieza'],
            ['codigo' => 'HE-0002', 'nombre' => 'Esmeril angular 4 1/2"', 'categoria' => 'Herramientas eléctricas', 'proveedor' => 0, 'compra' => 650.00, 'venta' => 980.00, 'stock' => 6, 'minimo' => 2, 'unidad' => 'pieza'],
            ['codigo' => 'TF-0001', 'nombre' => 'Clavo para concreto 2" (kilo)', 'categoria' => 'Tornillería y fijación', 'proveedor' => 1, 'compra' => 28.00, 'venta' => 45.00, 'stock' => 50, 'minimo' => 10, 'unidad' => 'kilo'],
            ['codigo' => 'TF-0002', 'nombre' => 'Tornillo p/tablaroca 1" (100 pzas)', 'categoria' => 'Tornillería y fijación', 'proveedor' => 1, 'compra' => 35.00, 'venta' => 58.00, 'stock' => 40, 'minimo' => 8, 'unidad' => 'paquete'],
            ['codigo' => 'TF-0003', 'nombre' => 'Taquete expansivo 3/8" (50 pzas)', 'categoria' => 'Tornillería y fijación', 'proveedor' => 1, 'compra' => 42.00, 'venta' => 69.00, 'stock' => 3, 'minimo' => 6, 'unidad' => 'paquete'],
            ['codigo' => 'PL-0001', 'nombre' => 'Tubo PVC hidráulico 1/2" (6 m)', 'categoria' => 'Plomería', 'proveedor' => 1, 'compra' => 65.00, 'venta' => 98.00, 'stock' => 30, 'minimo' => 8, 'unidad' => 'pieza'],
            ['codigo' => 'PL-0002', 'nombre' => 'Llave de paso 1/2" bronce', 'categoria' => 'Plomería', 'proveedor' => 1, 'compra' => 110.00, 'venta' => 175.00, 'stock' => 12, 'minimo' => 4, 'unidad' => 'pieza'],
            ['codigo' => 'EL-0001', 'nombre' => 'Cable THW cal. 12 (metro)', 'categoria' => 'Electricidad', 'proveedor' => 2, 'compra' => 9.50, 'venta' => 16.00, 'stock' => 500, 'minimo' => 100, 'unidad' => 'metro'],
            ['codigo' => 'EL-0002', 'nombre' => 'Contacto dúplex con tierra', 'categoria' => 'Electricidad', 'proveedor' => 2, 'compra' => 22.00, 'venta' => 38.00, 'stock' => 45, 'minimo' => 10, 'unidad' => 'pieza'],
            ['codigo' => 'EL-0003', 'nombre' => 'Foco LED 9W luz fría', 'categoria' => 'Electricidad', 'proveedor' => 2, 'compra' => 18.00, 'venta' => 32.00, 'stock' => 2, 'minimo' => 12, 'unidad' => 'pieza'],
            ['codigo' => 'PS-0001', 'nombre' => 'Pintura vinílica blanca 4 L', 'categoria' => 'Pinturas y solventes', 'proveedor' => 1, 'compra' => 240.00, 'venta' => 365.00, 'stock' => 14, 'minimo' => 4, 'unidad' => 'pieza'],
            ['codigo' => 'PS-0002', 'nombre' => 'Brocha 4" cerda natural', 'categoria' => 'Pinturas y solventes', 'proveedor' => 0, 'compra' => 45.00, 'venta' => 75.00, 'stock' => 20, 'minimo' => 5, 'unidad' => 'pieza'],
            ['codigo' => 'CO-0001', 'nombre' => 'Cemento gris 50 kg', 'categoria' => 'Construcción', 'proveedor' => 1, 'compra' => 185.00, 'venta' => 245.00, 'stock' => 60, 'minimo' => 15, 'unidad' => 'pieza'],
            ['codigo' => 'JA-0001', 'nombre' => 'Manguera reforzada 1/2" (rollo 15 m)', 'categoria' => 'Jardinería', 'proveedor' => 0, 'compra' => 145.00, 'venta' => 229.00, 'stock' => 10, 'minimo' => 3, 'unidad' => 'rollo'],
        ];

        foreach ($productos as $p) {
            Producto::updateOrCreate(
                ['codigo' => $p['codigo']],
                [
                    'nombre' => $p['nombre'],
                    'categoria_id' => $categorias[$p['categoria']]->id,
                    'proveedor_id' => $proveedores[$p['proveedor']]->id,
                    'precio_compra' => $p['compra'],
                    'precio_venta' => $p['venta'],
                    'stock' => $p['stock'],
                    'stock_minimo' => $p['minimo'],
                    'unidad' => $p['unidad'],
                    'activo' => true,
                ]
            );
        }

        // Clientes de ejemplo
        foreach ([
            ['nombre' => 'Constructora Hernández', 'telefono' => '777-123-4567', 'rfc' => 'CHE990101AB1'],
            ['nombre' => 'María López', 'telefono' => '777-765-4321'],
        ] as $c) {
            Cliente::updateOrCreate(['nombre' => $c['nombre']], $c);
        }
    }
}
