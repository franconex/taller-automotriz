<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalles_solicitud_compra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_compra_id')->constrained('solicitudes_compra')->cascadeOnDelete();
            $table->foreignId('repuesto_id')->constrained('repuestos');
            $table->unsignedInteger('cantidad_solicitada');
            $table->unsignedInteger('stock_actual')->default(0);
            $table->unsignedInteger('stock_minimo')->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['solicitud_compra_id', 'repuesto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles_solicitud_compra');
    }
};
