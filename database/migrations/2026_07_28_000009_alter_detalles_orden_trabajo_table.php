<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalles_orden_trabajo', function (Blueprint $table) {
            if (! Schema::hasColumn('detalles_orden_trabajo', 'subservicio_id')) {
                $table->foreignId('subservicio_id')->nullable()->after('servicio_id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('detalles_orden_trabajo', 'estado_autorizacion')) {
                $table->string('estado_autorizacion', 30)->default('aprobado')->after('subtotal');
            }
        });

        Schema::table('ordenes_trabajo', function (Blueprint $table) {
            if (! Schema::hasColumn('ordenes_trabajo', 'origen_ingreso')) {
                $table->string('origen_ingreso', 30)->default('cita')->after('observaciones');
            }
        });
    }

    public function down(): void
    {
        Schema::table('detalles_orden_trabajo', function (Blueprint $table) {
            $table->dropForeign(['subservicio_id']);
            $table->dropColumn(['subservicio_id', 'estado_autorizacion']);
        });
        Schema::table('ordenes_trabajo', function (Blueprint $table) {
            $table->dropColumn('origen_ingreso');
        });
    }
};
