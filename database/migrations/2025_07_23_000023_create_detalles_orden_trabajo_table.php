<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalles_orden_trabajo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_trabajo_id')->constrained('ordenes_trabajo')->cascadeOnDelete();
            $table->string('tipo', 20);
            $table->foreignId('servicio_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('repuesto_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('asignacion_trabajo_id')->nullable()->constrained('asignaciones_trabajo')->nullOnDelete();
            $table->text('descripcion');
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->decimal('precio_unitario', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles_orden_trabajo');
    }
};