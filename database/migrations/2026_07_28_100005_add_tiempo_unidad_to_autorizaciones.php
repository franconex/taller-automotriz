<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('autorizaciones', 'tiempo_estimado_unidad')) {
            Schema::table('autorizaciones', function (Blueprint $table) {
                $table->string('tiempo_estimado_unidad', 10)->nullable()->after('tiempo_estimado_minutos')->comment('minutos|horas|dias');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('autorizaciones', 'tiempo_estimado_unidad')) {
            Schema::table('autorizaciones', function (Blueprint $table) {
                $table->dropColumn('tiempo_estimado_unidad');
            });
        }
    }
};
