<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->dropColumn('tiempo_entrega_dias');
            $table->string('map_url', 500)->nullable()->after('direccion');
        });
    }

    public function down(): void
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->unsignedSmallInteger('tiempo_entrega_dias')->nullable()->after('nit');
            $table->dropColumn('map_url');
        });
    }
};
