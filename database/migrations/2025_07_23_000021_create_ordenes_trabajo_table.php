<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_trabajo', function (Blueprint $table) {
            $table->id();
            $table->string('numero_orden', 30)->unique();
            $table->foreignId('cliente_id')->constrained()->restrictOnDelete();
            $table->foreignId('vehiculo_id')->constrained()->restrictOnDelete();
            $table->foreignId('sucursal_id')->constrained('sucursales')->restrictOnDelete();
            $table->foreignId('usuario_recepcion_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cita_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->timestamp('fecha_emision');
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();
            $table->timestamp('fecha_entrega')->nullable();
            $table->unsignedInteger('kilometraje_ingreso')->default(0);
            $table->text('descripcion_problema');
            $table->text('diagnostico_general')->nullable();
            $table->string('estado', 20)->default('recibida');
            $table->decimal('subtotal_servicios', 12, 2)->default(0);
            $table->decimal('subtotal_repuestos', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('total_general', 12, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('estado');
            $table->index('fecha_emision');
            $table->index('sucursal_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_trabajo');
    }
};