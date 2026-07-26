<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repuestos', function (Blueprint $table) {
            $table->string('codigo_barras', 50)->nullable()->after('codigo');
            $table->string('codigo_fabricante', 50)->nullable()->after('codigo_barras');
            $table->string('marca', 100)->nullable()->after('categoria');
            $table->string('unidad_medida', 30)->nullable()->after('marca');
            $table->string('imagen', 255)->nullable()->after('unidad_medida');
        });
    }

    public function down(): void
    {
        Schema::table('repuestos', function (Blueprint $table) {
            $table->dropColumn(['codigo_barras', 'codigo_fabricante', 'marca', 'unidad_medida', 'imagen']);
        });
    }
};
