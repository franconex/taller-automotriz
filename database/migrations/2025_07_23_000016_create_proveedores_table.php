<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_empresa', 150);
            $table->string('contacto', 100)->nullable();
            $table->string('telefono', 20);
            $table->string('email', 100)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('nit', 30)->nullable()->unique();
            $table->unsignedSmallInteger('tiempo_entrega_dias')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('nombre_empresa');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};