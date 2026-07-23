<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventario_id')->constrained()->restrictOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('orden_trabajo_id')->nullable()->constrained('ordenes_trabajo')->nullOnDelete();
            $table->string('tipo', 20);
            $table->unsignedInteger('cantidad');
            $table->unsignedInteger('existencia_anterior')->default(0);
            $table->unsignedInteger('existencia_nueva')->default(0);
            $table->text('motivo');
            $table->timestamp('fecha_movimiento');
            $table->timestamps();

            $table->index('tipo');
            $table->index('fecha_movimiento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};