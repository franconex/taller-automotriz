<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // autorizaciones: make orden_trabajo_id nullable
        DB::statement('ALTER TABLE `autorizaciones` DROP FOREIGN KEY `autorizaciones_orden_trabajo_id_foreign`');
        DB::statement('ALTER TABLE `autorizaciones` MODIFY `orden_trabajo_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `autorizaciones` ADD CONSTRAINT `autorizaciones_orden_trabajo_id_foreign` FOREIGN KEY (`orden_trabajo_id`) REFERENCES `ordenes_trabajo` (`id`) ON DELETE CASCADE');

        // orden_servicios: make orden_trabajo_id nullable (no FK exists on this column)
        DB::statement('ALTER TABLE `orden_servicios` MODIFY `orden_trabajo_id` BIGINT UNSIGNED NULL');

        // orden_repuestos: make orden_trabajo_id nullable
        DB::statement('ALTER TABLE `orden_repuestos` DROP FOREIGN KEY `orden_repuestos_orden_trabajo_id_foreign`');
        DB::statement('ALTER TABLE `orden_repuestos` MODIFY `orden_trabajo_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `orden_repuestos` ADD CONSTRAINT `orden_repuestos_orden_trabajo_id_foreign` FOREIGN KEY (`orden_trabajo_id`) REFERENCES `ordenes_trabajo` (`id`) ON DELETE CASCADE');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `orden_repuestos` DROP FOREIGN KEY `orden_repuestos_orden_trabajo_id_foreign`');
        DB::statement('ALTER TABLE `orden_repuestos` MODIFY `orden_trabajo_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `orden_repuestos` ADD CONSTRAINT `orden_repuestos_orden_trabajo_id_foreign` FOREIGN KEY (`orden_trabajo_id`) REFERENCES `ordenes_trabajo` (`id`) ON DELETE CASCADE');

        DB::statement('ALTER TABLE `orden_servicios` MODIFY `orden_trabajo_id` BIGINT UNSIGNED NOT NULL');

        DB::statement('ALTER TABLE `autorizaciones` DROP FOREIGN KEY `autorizaciones_orden_trabajo_id_foreign`');
        DB::statement('ALTER TABLE `autorizaciones` MODIFY `orden_trabajo_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `autorizaciones` ADD CONSTRAINT `autorizaciones_orden_trabajo_id_foreign` FOREIGN KEY (`orden_trabajo_id`) REFERENCES `ordenes_trabajo` (`id`) ON DELETE CASCADE');
    }
};
