<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("UPDATE `stamps` SET `for_sale` = 0 WHERE `for_sale` IS NULL");
        DB::statement("ALTER TABLE `stamps` MODIFY `for_sale` TINYINT(1) NOT NULL DEFAULT 0");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `stamps` MODIFY `for_sale` TINYINT(1) NULL DEFAULT NULL");
    }
};
