<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_servicio_id')->constrained('tipos_servicio')->restrictOnDelete();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->decimal('precio_base', 12, 2)->default(0);
            $table->unsignedSmallInteger('duracion_estimada_minutos')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tipo_servicio_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};