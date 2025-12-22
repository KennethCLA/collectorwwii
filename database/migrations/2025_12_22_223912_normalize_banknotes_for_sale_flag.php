<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Data cleanup: NULL -> 0
        DB::statement("UPDATE `banknotes` SET `for_sale` = 0 WHERE `for_sale` IS NULL");

        // 2) Schema: NOT NULL + DEFAULT 0
        DB::statement("ALTER TABLE `banknotes` MODIFY `for_sale` TINYINT NOT NULL DEFAULT 0");
    }

    public function down(): void
    {
        // Terug naar nullable (default NULL) zoals oorspronkelijk
        DB::statement("ALTER TABLE `banknotes` MODIFY `for_sale` TINYINT NULL DEFAULT NULL");
    }
};
