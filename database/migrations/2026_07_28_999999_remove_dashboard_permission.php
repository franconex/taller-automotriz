<?php

use App\Models\Permiso;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Permiso::where('codigo', 'dashboard.ver')->delete();
    }

    public function down(): void
    {
        Permiso::firstOrCreate(
            ['codigo' => 'dashboard.ver'],
            [
                'nombre' => 'Ver Dashboard',
                'modulo' => 'dashboard',
                'descripcion' => null,
            ]
        );
    }
};
