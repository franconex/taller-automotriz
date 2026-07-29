<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar cita_id nullable a autorizaciones (para cotizar desde cita sin orden)
        if (! Schema::hasColumn('autorizaciones', 'cita_id')) {
            Schema::table('autorizaciones', function (Blueprint $table) {
                $table->foreignId('cita_id')->nullable()->after('orden_trabajo_id')->constrained()->cascadeOnDelete();
            });
        }

        // Agregar autorizacion_id nullable a orden_servicios (para cotización)
        if (! Schema::hasColumn('orden_servicios', 'autorizacion_id')) {
            Schema::table('orden_servicios', function (Blueprint $table) {
                $table->foreignId('autorizacion_id')->nullable()->after('orden_trabajo_id')->constrained('autorizaciones')->cascadeOnDelete();
                $table->foreignId('mecanico_id')->nullable()->after('autorizacion_id')->constrained()->restrictOnDelete();
            });
        }

        // Agregar autorizacion_id nullable a orden_repuestos (para cotización)
        if (! Schema::hasColumn('orden_repuestos', 'autorizacion_id')) {
            Schema::table('orden_repuestos', function (Blueprint $table) {
                $table->foreignId('autorizacion_id')->nullable()->after('orden_trabajo_id')->constrained('autorizaciones')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orden_repuestos', 'autorizacion_id')) {
            Schema::table('orden_repuestos', function (Blueprint $table) {
                $table->dropForeign(['autorizacion_id']);
                $table->dropColumn('autorizacion_id');
            });
        }
        if (Schema::hasColumn('orden_servicios', 'autorizacion_id')) {
            Schema::table('orden_servicios', function (Blueprint $table) {
                $table->dropForeign(['autorizacion_id']);
                $table->dropForeign(['mecanico_id']);
                $table->dropColumn(['autorizacion_id', 'mecanico_id']);
            });
        }
        if (Schema::hasColumn('autorizaciones', 'cita_id')) {
            Schema::table('autorizaciones', function (Blueprint $table) {
                $table->dropForeign(['cita_id']);
                $table->dropColumn('cita_id');
            });
        }
    }
};
