<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            $table->string('marca', 100)->nullable()->after('modelo_vehiculo_id');
            $table->string('modelo', 100)->nullable()->after('marca');
        });

        Schema::table('vehiculos', function (Blueprint $table) {
            $table->dropForeign('vehiculos_modelo_vehiculo_id_foreign');
        });

        Schema::table('vehiculos', function (Blueprint $table) {
            $table->foreignId('modelo_vehiculo_id')->nullable()->change();
            $table->foreign('modelo_vehiculo_id')->references('id')->on('modelos_vehiculos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            $table->dropColumn(['marca', 'modelo']);
        });
    }
};
