<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            $table->foreignId('tipo_vehiculo_id')->nullable()->constrained('tipos_vehiculo')->nullOnDelete();
            $table->foreignId('tipo_uso_id')->nullable()->constrained('tipos_uso')->nullOnDelete();
            $table->string('numero_motor', 100)->nullable()->after('numero_chasis');
            $table->string('combustible', 50)->nullable();
            $table->string('transmision', 50)->nullable();
            $table->string('cilindrada', 50)->nullable();
            $table->unsignedSmallInteger('capacidad_pasajeros')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            $table->dropForeign(['tipo_vehiculo_id']);
            $table->dropForeign(['tipo_uso_id']);
            $table->dropColumn([
                'tipo_vehiculo_id', 'tipo_uso_id', 'numero_motor',
                'combustible', 'transmision', 'cilindrada', 'capacidad_pasajeros',
            ]);
        });
    }
};
