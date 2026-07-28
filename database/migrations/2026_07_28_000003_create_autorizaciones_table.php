<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('autorizaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_trabajo_id');
            $table->unsignedBigInteger('usuario_solicitante_id');
            $table->string('titulo', 200);
            $table->text('descripcion');
            $table->decimal('importe', 12, 2)->default(0);
            $table->string('estado', 30)->default('pendiente');
            $table->dateTime('fecha_solicitud');
            $table->dateTime('fecha_respuesta')->nullable();
            $table->text('comentario_cliente')->nullable();
            $table->unsignedBigInteger('respondido_por_id')->nullable();
            $table->timestamps();

            $table->foreign('orden_trabajo_id')->references('id')->on('ordenes_trabajo')->cascadeOnDelete();
            $table->foreign('usuario_solicitante_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('respondido_por_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('autorizaciones');
    }
};
