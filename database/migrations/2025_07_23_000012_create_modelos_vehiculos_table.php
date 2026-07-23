<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modelos_vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marca_vehiculo_id')->constrained('marcas_vehiculos')->restrictOnDelete();
            $table->string('nombre', 80);
            $table->year('anio_lanzamiento')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->unique(['marca_vehiculo_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modelos_vehiculos');
    }
};