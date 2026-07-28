<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventarios', function (Blueprint $table) {
            if (Schema::hasColumn('inventarios', 'ubicacion_id')) {
                $table->dropConstrainedForeignId('ubicacion_id');
            }
        });

        Schema::dropIfExists('ubicaciones_inventario');
    }

    public function down(): void
    {
        Schema::create('ubicaciones_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();
            $table->string('almacen', 100);
            $table->string('pasillo', 50)->nullable();
            $table->string('estante', 50)->nullable();
            $table->string('nivel', 30)->nullable();
            $table->string('casillero', 30)->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::table('inventarios', function (Blueprint $table) {
            $table->foreignId('ubicacion_id')->nullable()->constrained('ubicaciones_inventario')->nullOnDelete();
        });
    }
};
