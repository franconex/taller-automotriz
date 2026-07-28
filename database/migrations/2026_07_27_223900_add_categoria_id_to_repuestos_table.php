<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repuestos', function (Blueprint $table) {
            if (!Schema::hasColumn('repuestos', 'categoria_id')) {
                $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('repuestos', function (Blueprint $table) {
            if (Schema::hasColumn('repuestos', 'categoria_id')) {
                $table->dropConstrainedForeignId('categoria_id');
            }
        });
    }
};
