<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaciones_trabajo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_trabajo_id')->constrained('ordenes_trabajo')->restrictOnDelete();
            $table->foreignId('mecanico_id')->constrained()->restrictOnDelete();
            $table->foreignId('usuario_asignador_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('actividad_asignada');
            $table->string('prioridad', 20)->default('normal');
            $table->string('estado', 20)->default('pendiente');
            $table->timestamp('fecha_asignacion');
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_finalizacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['mecanico_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones_trabajo');
    }
};