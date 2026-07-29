<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('cliente_id')->nullable()->unique()->after('empleado_id');
            $table->foreign('cliente_id')->references('id')->on('clientes')->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sucursal_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('sucursal_id')->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropColumn('cliente_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sucursal_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('sucursal_id')->nullable(false)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->restrictOnDelete();
        });
    }
};
