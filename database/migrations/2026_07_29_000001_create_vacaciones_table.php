<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_solicitante_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('usuario_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('motivo', 500);
            $table->string('estado', 20)->default('pendiente');
            $table->text('respuesta_admin')->nullable();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->timestamps();

            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacaciones');
    }
};
