<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users');
            $table->string('accion', 50);
            $table->string('entidad_afectada', 50);
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->json('valores_anteriores')->nullable();
            $table->json('valores_nuevos')->nullable();
            $table->text('detalle')->nullable();
            $table->string('direccion_ip', 45)->nullable();
            $table->string('navegador')->nullable();
            $table->string('ruta')->nullable();
            $table->timestamps();

            $table->index(['entidad_afectada', 'entidad_id']);
            $table->index('accion');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};
