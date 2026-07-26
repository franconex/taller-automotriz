<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_compra', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->foreignId('usuario_solicitante_id')->constrained('users');
            $table->foreignId('usuario_autoriza_id')->nullable()->constrained('users');
            $table->string('prioridad', 20)->default('media');
            $table->string('estado', 30)->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->dateTime('fecha_solicitud');
            $table->dateTime('fecha_aprobacion')->nullable();
            $table->timestamps();

            $table->index('estado');
            $table->index('prioridad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_compra');
    }
};
