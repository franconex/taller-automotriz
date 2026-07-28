<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->unique()->after('cliente_id');
            }
            if (! Schema::hasColumn('users', 'google_avatar')) {
                $table->string('google_avatar', 500)->nullable()->after('google_id');
            }
            if (! Schema::hasColumn('users', 'origen_registro')) {
                $table->string('origen_registro', 30)->default('manual')->after('google_avatar');
            }
            if (! Schema::hasColumn('users', 'debe_cambiar_password')) {
                $table->boolean('debe_cambiar_password')->default(false)->after('origen_registro');
            }
            if (Schema::hasColumn('users', 'password')) {
                $table->string('password')->nullable()->change();
            }
            if (Schema::hasColumn('users', 'username')) {
                $table->string('username', 50)->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'google_avatar', 'origen_registro', 'debe_cambiar_password']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
