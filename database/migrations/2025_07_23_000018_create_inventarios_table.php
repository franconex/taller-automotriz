<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->restrictOnDelete();
            $table->foreignId('repuesto_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('cantidad_actual')->default(0);
            $table->unsignedInteger('cantidad_reservada')->default(0);
            $table->timestamp('fecha_actualizacion')->nullable();
            $table->timestamps();

            $table->unique(['sucursal_id', 'repuesto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};