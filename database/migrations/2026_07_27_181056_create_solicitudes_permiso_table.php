<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_permiso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_solicitante_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('permiso_id')->constrained()->restrictOnDelete();
            $table->text('motivo');
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->text('respuesta_admin')->nullable();
            $table->foreignId('usuario_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_permiso');
    }
};
