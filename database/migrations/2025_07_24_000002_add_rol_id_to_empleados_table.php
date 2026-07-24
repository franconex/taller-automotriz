<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->foreignId('rol_id')
                ->after('sucursal_id')
                ->constrained('roles')
                ->restrictOnDelete();

            $table->index('rol_id');
        });
    }

    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropForeign(['rol_id']);
            $table->dropIndex(['rol_id']);
            $table->dropColumn('rol_id');
        });
    }
};
