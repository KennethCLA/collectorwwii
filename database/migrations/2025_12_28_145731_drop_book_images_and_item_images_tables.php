<?php
// database/migrations/2025_12_28_145731_drop_book_images_and_item_images_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('book_images');
        Schema::dropIfExists('item_images');
        Schema::dropIfExists('_tmp_fix_book_images');
        Schema::dropIfExists('_tmp_fix_item_images');
    }

    public function down(): void
    {
        // intentionally not reversible
    }
};
