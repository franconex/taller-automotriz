<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_compra_id')->constrained('solicitudes_compra')->cascadeOnDelete();
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->foreignId('usuario_id')->constrained('users');
            $table->string('medio_contacto', 30);
            $table->string('nombre_contacto', 150)->nullable();
            $table->dateTime('fecha_cotizacion');
            $table->date('fecha_vencimiento')->nullable();
            $table->string('estado', 30)->default('pendiente');
            $table->string('motivo_seleccion', 50)->nullable();
            $table->string('motivo_seleccion_otro', 255)->nullable();
            $table->text('observaciones')->nullable();
            $table->string('archivo', 255)->nullable();
            $table->timestamps();

            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};
