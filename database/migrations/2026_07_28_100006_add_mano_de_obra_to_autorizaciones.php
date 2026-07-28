<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('autorizaciones', 'mano_de_obra')) {
            Schema::table('autorizaciones', function (Blueprint $table) {
                $table->decimal('mano_de_obra', 12, 2)->nullable()->after('importe');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('autorizaciones', 'mano_de_obra')) {
            Schema::table('autorizaciones', function (Blueprint $table) {
                $table->dropColumn('mano_de_obra');
            });
        }
    }
};
