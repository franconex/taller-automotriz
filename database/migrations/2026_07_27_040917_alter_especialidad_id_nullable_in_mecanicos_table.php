<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE mecanicos DROP FOREIGN KEY mecanicos_especialidad_id_foreign');
        DB::statement('ALTER TABLE mecanicos MODIFY especialidad_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE mecanicos ADD CONSTRAINT mecanicos_especialidad_id_foreign FOREIGN KEY (especialidad_id) REFERENCES especialidades(id) ON DELETE SET NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE mecanicos DROP FOREIGN KEY mecanicos_especialidad_id_foreign');
        DB::statement('ALTER TABLE mecanicos MODIFY especialidad_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE mecanicos ADD CONSTRAINT mecanicos_especialidad_id_foreign FOREIGN KEY (especialidad_id) REFERENCES especialidades(id) ON DELETE RESTRICT');
    }
};
