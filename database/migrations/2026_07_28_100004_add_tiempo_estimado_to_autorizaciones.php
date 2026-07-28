<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('autorizaciones', 'tiempo_estimado_minutos')) {
            Schema::table('autorizaciones', function (Blueprint $table) {
                $table->unsignedSmallInteger('tiempo_estimado_minutos')->nullable()->after('importe');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('autorizaciones', 'tiempo_estimado_minutos')) {
            Schema::table('autorizaciones', function (Blueprint $table) {
                $table->dropColumn('tiempo_estimado_minutos');
            });
        }
    }
};
