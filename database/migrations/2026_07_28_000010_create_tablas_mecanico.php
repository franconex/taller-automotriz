<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('diagnosticos_orden')) {
            Schema::create('diagnosticos_orden', function (Blueprint $table) {
                $table->id();
                $table->foreignId('orden_trabajo_id')->constrained('ordenes_trabajo')->cascadeOnDelete();
                $table->foreignId('mecanico_id')->constrained()->restrictOnDelete();
                $table->text('problema_encontrado')->nullable();
                $table->text('causa_probable')->nullable();
                $table->text('recomendacion')->nullable();
                $table->text('observacion_cliente')->nullable();
                $table->text('observacion_interna')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('avances_orden')) {
            Schema::create('avances_orden', function (Blueprint $table) {
                $table->id();
                $table->foreignId('orden_trabajo_id')->constrained('ordenes_trabajo')->cascadeOnDelete();
                $table->foreignId('mecanico_id')->constrained()->restrictOnDelete();
                $table->string('titulo', 200);
                $table->text('descripcion')->nullable();
                $table->string('estado', 30)->default('en_proceso');
                $table->unsignedTinyInteger('porcentaje')->nullable();
                $table->text('nota_cliente')->nullable();
                $table->text('nota_interna')->nullable();
                $table->boolean('visible_cliente')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('orden_servicios')) {
            Schema::create('orden_servicios', function (Blueprint $table) {
                $table->id();
                $table->foreignId('orden_trabajo_id')->constrained('ordenes_trabajo')->cascadeOnDelete();
                $table->foreignId('servicio_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('subservicio_id')->nullable()->constrained()->nullOnDelete();
                $table->string('nombre_servicio', 200);
                $table->string('nombre_subservicio', 200)->nullable();
                $table->decimal('precio_base', 12, 2)->default(0);
                $table->unsignedSmallInteger('tiempo_estimado_minutos')->nullable();
                $table->text('observacion')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('orden_repuestos')) {
            Schema::create('orden_repuestos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('orden_trabajo_id')->constrained('ordenes_trabajo')->cascadeOnDelete();
                $table->foreignId('repuesto_id')->constrained()->restrictOnDelete();
                $table->foreignId('mecanico_id')->constrained()->restrictOnDelete();
                $table->decimal('cantidad', 10, 2)->default(1);
                $table->string('estado', 30)->default('solicitado');
                $table->text('motivo')->nullable();
                $table->decimal('precio_unitario_snapshot', 12, 2)->default(0);
                $table->timestamps();
            });
        }

        // Agregar columna origen_ingreso si no existe
        if (! Schema::hasColumn('ordenes_trabajo', 'origen_ingreso')) {
            Schema::table('ordenes_trabajo', function (Blueprint $table) {
                $table->string('origen_ingreso', 30)->default('cita')->after('observaciones');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_repuestos');
        Schema::dropIfExists('orden_servicios');
        Schema::dropIfExists('avances_orden');
        Schema::dropIfExists('diagnosticos_orden');
        if (Schema::hasColumn('ordenes_trabajo', 'origen_ingreso')) {
            Schema::table('ordenes_trabajo', function (Blueprint $table) {
                $table->dropColumn('origen_ingreso');
            });
        }
    }
};
