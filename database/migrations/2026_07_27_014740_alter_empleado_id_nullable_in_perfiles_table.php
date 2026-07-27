<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE perfiles DROP FOREIGN KEY perfiles_empleado_id_foreign');
        DB::statement('ALTER TABLE perfiles MODIFY empleado_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE perfiles ADD CONSTRAINT perfiles_empleado_id_foreign FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE perfiles DROP FOREIGN KEY perfiles_empleado_id_foreign');
        DB::statement('ALTER TABLE perfiles MODIFY empleado_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE perfiles ADD CONSTRAINT perfiles_empleado_id_foreign FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE');
    }
};
