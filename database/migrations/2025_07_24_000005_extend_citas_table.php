<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->foreignId('servicio_id')
                ->nullable()
                ->after('tipo')
                ->constrained('servicios')
                ->nullOnDelete();

            $table->foreignId('mecanico_id')
                ->nullable()
                ->after('servicio_id')
                ->constrained('mecanicos')
                ->nullOnDelete();

            $table->time('hora_fin')
                ->nullable()
                ->after('hora');

            $table->unsignedSmallInteger('duracion_minutos')
                ->nullable()
                ->after('hora_fin');

            // Reprogamación
            $table->foreignId('reprogramado_por_id')
                ->nullable()
                ->after('observaciones')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('reprogramado_en')->nullable()->after('reprogramado_por_id');
            $table->text('motivo_reprogramacion')->nullable()->after('reprogramado_en');

            // Cancelación
            $table->foreignId('cancelado_por_id')
                ->nullable()
                ->after('motivo_reprogramacion')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('cancelado_en')->nullable()->after('cancelado_por_id');
            $table->text('cancelado_motivo')->nullable()->after('cancelado_en');

            // Estado anterior para auditoría
            $table->string('estado_anterior', 20)->nullable()->after('cancelado_motivo');

            $table->index(['fecha', 'estado']);
            $table->index('mecanico_id');
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropForeign(['servicio_id']);
            $table->dropForeign(['mecanico_id']);
            $table->dropForeign(['reprogramado_por_id']);
            $table->dropForeign(['cancelado_por_id']);

            $table->dropIndex(['fecha', 'estado']);
            $table->dropIndex(['mecanico_id']);

            $table->dropColumn([
                'servicio_id',
                'mecanico_id',
                'hora_fin',
                'duracion_minutos',
                'reprogramado_por_id',
                'reprogramado_en',
                'motivo_reprogramacion',
                'cancelado_por_id',
                'cancelado_en',
                'cancelado_motivo',
                'estado_anterior',
            ]);
        });
    }
};
