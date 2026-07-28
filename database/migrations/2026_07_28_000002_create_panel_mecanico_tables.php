<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('asignaciones_trabajo', 'porcentaje_avance')) {
            Schema::table('asignaciones_trabajo', function (Blueprint $table) {
                $table->tinyInteger('porcentaje_avance')->default(0)->after('observaciones');
                $table->text('proximo_paso')->nullable()->after('porcentaje_avance');
                $table->text('diagnostico_mecanico')->nullable()->after('proximo_paso');
            });
        }

        if (Schema::hasTable('notas_trabajo')) {
            Schema::dropIfExists('evidencias_trabajo');
            Schema::dropIfExists('notas_trabajo');
        }

        Schema::create('notas_trabajo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asignacion_trabajo_id');
            $table->unsignedBigInteger('usuario_id');
            $table->text('contenido');
            $table->boolean('visible_cliente')->default(false);
            $table->timestamps();

            $table->foreign('asignacion_trabajo_id', 'fk_notas_asignacion')
                ->references('id')->on('asignaciones_trabajo')->cascadeOnDelete();
            $table->foreign('usuario_id', 'fk_notas_usuario')
                ->references('id')->on('users')->restrictOnDelete();
        });

        Schema::create('evidencias_trabajo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asignacion_trabajo_id');
            $table->unsignedBigInteger('usuario_id');
            $table->string('archivo', 255);
            $table->string('descripcion', 255)->nullable();
            $table->timestamps();

            $table->foreign('asignacion_trabajo_id', 'fk_evidencias_asignacion')
                ->references('id')->on('asignaciones_trabajo')->cascadeOnDelete();
            $table->foreign('usuario_id', 'fk_evidencias_usuario')
                ->references('id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidencias_trabajo');
        Schema::dropIfExists('notas_trabajo');

        if (Schema::hasColumn('asignaciones_trabajo', 'porcentaje_avance')) {
            Schema::table('asignaciones_trabajo', function (Blueprint $table) {
                $table->dropColumn(['porcentaje_avance', 'proximo_paso', 'diagnostico_mecanico']);
            });
        }
    }
};
