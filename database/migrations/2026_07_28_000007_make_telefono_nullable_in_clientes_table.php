<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('telefono', 20)->nullable()->change();
            $table->string('ci', 20)->nullable()->change();
            $table->string('email', 100)->nullable()->change();
            $table->string('direccion', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('telefono', 20)->nullable(false)->change();
            $table->string('ci', 20)->nullable(false)->change();
            $table->string('email', 100)->nullable(false)->change();
            $table->string('direccion', 255)->nullable(false)->change();
        });
    }
};
