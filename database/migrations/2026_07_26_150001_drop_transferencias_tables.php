<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->dropConstrainedForeignId('transferencia_id');
            $table->dropConstrainedForeignId('repuesto_id');
            $table->dropConstrainedForeignId('sucursal_id');
            $table->dropColumn('codigo');
        });

        Schema::dropIfExists('detalles_transferencia');
        Schema::dropIfExists('transferencias_inventario');
    }

    public function down(): void
    {
        Schema::create('transferencias_inventario', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->foreignId('sucursal_origen_id')->constrained('sucursales');
            $table->foreignId('sucursal_destino_id')->constrained('sucursales');
            $table->string('estado', 30)->default('borrador');
            $table->foreignId('solicitada_por')->constrained('users');
            $table->foreignId('aprobada_por')->nullable()->constrained('users');
            $table->foreignId('preparada_por')->nullable()->constrained('users');
            $table->foreignId('enviada_por')->nullable()->constrained('users');
            $table->foreignId('recibida_por')->nullable()->constrained('users');
            $table->dateTime('fecha_solicitud');
            $table->dateTime('fecha_aprobacion')->nullable();
            $table->dateTime('fecha_envio')->nullable();
            $table->dateTime('fecha_recepcion')->nullable();
            $table->text('motivo')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('detalles_transferencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transferencia_id')->constrained('transferencias_inventario')->cascadeOnDelete();
            $table->foreignId('repuesto_id')->constrained('repuestos');
            $table->unsignedInteger('cantidad_solicitada');
            $table->unsignedInteger('cantidad_aprobada')->nullable();
            $table->unsignedInteger('cantidad_preparada')->nullable();
            $table->unsignedInteger('cantidad_enviada')->nullable();
            $table->unsignedInteger('cantidad_recibida')->nullable();
            $table->unsignedInteger('cantidad_danada')->nullable();
            $table->unsignedInteger('cantidad_faltante')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->string('codigo', 30)->nullable()->after('id');
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete()->after('inventario_id');
            $table->foreignId('repuesto_id')->nullable()->constrained('repuestos')->nullOnDelete()->after('sucursal_id');
            $table->foreignId('transferencia_id')->nullable()->constrained('transferencias_inventario')->nullOnDelete()->after('orden_trabajo_id');
        });
    }
};
