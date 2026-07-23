<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_trabajo_id')->constrained('ordenes_trabajo')->restrictOnDelete();
            $table->foreignId('metodo_pago_id')->constrained('metodos_pago')->restrictOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_pago');
            $table->decimal('monto', 12, 2)->default(0);
            $table->string('numero_comprobante', 50)->nullable()->unique();
            $table->string('referencia', 255)->nullable();
            $table->string('estado', 20)->default('confirmado');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('fecha_pago');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};