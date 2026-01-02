<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('book_images');
        Schema::dropIfExists('item_images');
    }

    public function down(): void
    {
        // Intentionally not reversible: images now live in media_files
    }
};
