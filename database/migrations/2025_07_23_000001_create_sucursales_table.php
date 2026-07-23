<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sucursales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('direccion', 255);
            $table->string('telefono', 20);
            $table->string('horario_atencion', 150)->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursales');
    }
};