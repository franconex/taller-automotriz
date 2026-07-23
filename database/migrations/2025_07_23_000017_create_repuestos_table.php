<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repuestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('costo_compra', 12, 2)->default(0);
            $table->decimal('precio_venta', 12, 2)->default(0);
            $table->unsignedInteger('stock_minimo')->default(0);
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('nombre');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repuestos');
    }
};