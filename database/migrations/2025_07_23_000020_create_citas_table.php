<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->restrictOnDelete();
            $table->foreignId('vehiculo_id')->constrained()->restrictOnDelete();
            $table->foreignId('sucursal_id')->constrained('sucursales')->restrictOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha');
            $table->time('hora');
            $table->string('tipo', 50);
            $table->text('descripcion_problema');
            $table->string('estado', 20)->default('pendiente');
            $table->boolean('deja_vehiculo')->default(false);
            $table->decimal('costo_consulta', 12, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['fecha', 'hora', 'sucursal_id']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};