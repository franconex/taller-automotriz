<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('cliente_id')->constrained()->restrictOnDelete();
            $table->string('numero', 30)->unique();
            $table->timestamp('fecha_emision');
            $table->string('nit_ci', 30)->nullable();
            $table->string('razon_social', 150)->nullable();
            $table->decimal('monto_total', 12, 2)->default(0);
            $table->string('estado', 20)->default('emitido');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprobantes');
    }
};