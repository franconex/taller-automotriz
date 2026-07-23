<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->restrictOnDelete();
            $table->string('nombre_completo', 150);
            $table->string('ci', 20)->unique();
            $table->string('telefono', 20);
            $table->string('email', 100)->nullable()->unique();
            $table->string('direccion', 255)->nullable();
            $table->string('cargo', 80);
            $table->date('fecha_contratacion')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('nombre_completo');
            $table->index('cargo');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};