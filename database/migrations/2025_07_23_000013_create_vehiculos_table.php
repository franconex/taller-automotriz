<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->restrictOnDelete();
            $table->foreignId('modelo_vehiculo_id')->constrained('modelos_vehiculos')->restrictOnDelete();
            $table->string('placa', 20)->unique();
            $table->year('anio')->nullable();
            $table->string('color', 50)->nullable();
            $table->string('numero_chasis', 50)->nullable()->unique();
            $table->unsignedInteger('kilometraje_actual')->default(0);
            $table->text('observaciones')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('cliente_id');
            $table->index('placa');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};