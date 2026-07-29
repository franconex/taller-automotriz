<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('subservicios')) {
            Schema::create('subservicios', function (Blueprint $table) {
                $table->id();
                $table->foreignId('servicio_id')->constrained()->cascadeOnDelete();
                $table->string('nombre', 200);
                $table->text('descripcion')->nullable();
                $table->decimal('precio_base', 12, 2)->default(0);
                $table->unsignedSmallInteger('duracion_estimada_minutos')->nullable();
                $table->boolean('requiere_diagnostico')->default(false);
                $table->boolean('estado')->default(true);
                $table->timestamps();
                $table->unique(['servicio_id', 'nombre']);
            });
        }

        if (! Schema::hasTable('subservicio_repuesto')) {
            Schema::create('subservicio_repuesto', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subservicio_id')->constrained()->cascadeOnDelete();
                $table->foreignId('repuesto_id')->constrained()->restrictOnDelete();
                $table->decimal('cantidad_sugerida', 10, 2)->default(1);
                $table->timestamps();
                $table->unique(['subservicio_id', 'repuesto_id']);
            });
        }

        if (! Schema::hasTable('estimaciones_orden')) {
            Schema::create('estimaciones_orden', function (Blueprint $table) {
                $table->id();
                $table->foreignId('orden_trabajo_id')->constrained('ordenes_trabajo')->cascadeOnDelete();
                $table->foreignId('mecanico_id')->constrained()->restrictOnDelete();
                $table->unsignedSmallInteger('duracion_minima_minutos');
                $table->unsignedSmallInteger('duracion_maxima_minutos');
                $table->dateTime('fecha_estimada_entrega')->nullable();
                $table->text('motivo')->nullable();
                $table->text('observacion_cliente')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('estimaciones_orden');
        Schema::dropIfExists('subservicio_repuesto');
        Schema::dropIfExists('subservicios');
    }
};
