<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('autorizaciones', 'diagnostico_mecanico')) {
            Schema::table('autorizaciones', function (Blueprint $table) {
                $table->text('diagnostico_mecanico')->nullable()->after('descripcion');
                $table->string('foto_diagnostico', 500)->nullable()->after('diagnostico_mecanico');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('autorizaciones', 'foto_diagnostico')) {
            Schema::table('autorizaciones', function (Blueprint $table) {
                $table->dropColumn(['diagnostico_mecanico', 'foto_diagnostico']);
            });
        }
    }
};
