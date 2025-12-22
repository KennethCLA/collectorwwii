<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('coins', function (Blueprint $table) {
            // drop de 3 CASCADE lookups
            $table->dropForeign('coins_country_id_foreign');
            $table->dropForeign('coins_currency_id_foreign');
            $table->dropForeign('coins_nominal_value_id_foreign');

            // recreate als RESTRICT/NO ACTION
            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->noActionOnDelete();

            $table->foreign('currency_id')
                ->references('id')
                ->on('currencies')
                ->noActionOnDelete();

            $table->foreign('nominal_value_id')
                ->references('id')
                ->on('nominal_values')
                ->noActionOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('coins', function (Blueprint $table) {
            // rollback naar CASCADE zoals het nu in je schema zit
            $table->dropForeign('coins_country_id_foreign');
            $table->dropForeign('coins_currency_id_foreign');
            $table->dropForeign('coins_nominal_value_id_foreign');

            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->cascadeOnDelete();

            $table->foreign('currency_id')
                ->references('id')
                ->on('currencies')
                ->cascadeOnDelete();

            $table->foreign('nominal_value_id')
                ->references('id')
                ->on('nominal_values')
                ->cascadeOnDelete();
        });
    }
};
