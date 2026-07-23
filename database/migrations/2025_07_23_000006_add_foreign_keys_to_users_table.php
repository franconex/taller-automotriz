<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->restrictOnDelete();
            $table->foreign('rol_id')->references('id')->on('roles')->restrictOnDelete();
            $table->foreign('empleado_id')->references('id')->on('empleados')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sucursal_id']);
            $table->dropForeign(['rol_id']);
            $table->dropForeign(['empleado_id']);
        });
    }
};