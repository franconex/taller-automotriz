<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_compra', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->foreignId('solicitud_compra_id')->nullable()->constrained('solicitudes_compra')->nullOnDelete();
            $table->foreignId('cotizacion_id')->nullable()->constrained('cotizaciones')->nullOnDelete();
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->foreignId('usuario_solicitante_id')->constrained('users');
            $table->foreignId('usuario_aprobador_id')->nullable()->constrained('users');
            $table->dateTime('fecha_emision');
            $table->date('fecha_entrega_estimada')->nullable();
            $table->string('forma_pago', 100)->nullable();
            $table->decimal('subtotal', 14, 2);
            $table->decimal('costo_envio', 12, 2)->default(0);
            $table->decimal('impuesto', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('total', 14, 2);
            $table->string('estado', 30)->default('borrador');
            $table->string('enviada_medio', 30)->nullable();
            $table->dateTime('enviada_fecha')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_compra');
    }
};
