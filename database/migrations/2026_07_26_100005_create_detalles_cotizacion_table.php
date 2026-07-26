<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalles_cotizacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cotizacion_id')->constrained('cotizaciones')->cascadeOnDelete();
            $table->foreignId('repuesto_id')->constrained('repuestos');
            $table->unsignedInteger('cantidad_solicitada');
            $table->unsignedInteger('cantidad_disponible')->nullable();
            $table->string('marca_ofrecida', 150)->nullable();
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('impuesto', 12, 2)->default(0);
            $table->decimal('costo_envio', 12, 2)->default(0);
            $table->decimal('subtotal', 14, 2);
            $table->unsignedInteger('tiempo_entrega_dias')->nullable();
            $table->unsignedInteger('garantia_dias')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles_cotizacion');
    }
};
