<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Lubricantes y Aceites', 'slug' => 'lubricantes-aceites'],
            ['nombre' => 'Filtros', 'slug' => 'filtros'],
            ['nombre' => 'Frenos', 'slug' => 'frenos'],
            ['nombre' => 'Suspensión y Dirección', 'slug' => 'suspension-direccion'],
            ['nombre' => 'Motor y Transmisión', 'slug' => 'motor-transmision'],
            ['nombre' => 'Sistema Eléctrico', 'slug' => 'sistema-electrico'],
            ['nombre' => 'Carrocería y Accesorios', 'slug' => 'carroceria-accesorios'],
            ['nombre' => 'Neumáticos y Llantas', 'slug' => 'neumaticos-llantas'],
            ['nombre' => 'Sistema de Escape', 'slug' => 'sistema-escape'],
            ['nombre' => 'Refrigeración', 'slug' => 'refrigeracion'],
            ['nombre' => 'Herramientas', 'slug' => 'herramientas'],
            ['nombre' => 'Otros', 'slug' => 'otros'],
        ];

        foreach ($categorias as $cat) {
            Categoria::firstOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }
    }
}
