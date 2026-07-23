<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo', 150);
            $table->string('ci', 20)->nullable()->unique();
            $table->string('telefono', 20);
            $table->string('email', 100)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->timestamp('fecha_registro');
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('nombre_completo');
            $table->index('telefono');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};