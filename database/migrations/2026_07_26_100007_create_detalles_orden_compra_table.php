<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalles_orden_compra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_compra_id')->constrained('ordenes_compra')->cascadeOnDelete();
            $table->foreignId('repuesto_id')->constrained('repuestos');
            $table->unsignedInteger('cantidad_solicitada');
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('impuesto', 12, 2)->default(0);
            $table->decimal('subtotal', 14, 2);
            $table->unsignedInteger('cantidad_recibida')->default(0);
            $table->unsignedInteger('cantidad_aceptada')->default(0);
            $table->unsignedInteger('cantidad_rechazada')->default(0);
            $table->text('motivo_rechazo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles_orden_compra');
    }
};
