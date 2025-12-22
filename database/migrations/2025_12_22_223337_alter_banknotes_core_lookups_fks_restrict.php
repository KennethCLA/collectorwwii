<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('banknotes', function (Blueprint $table) {
            // Drop de 5 CASCADE lookups
            $table->dropForeign('banknotes_country_id_foreign');
            $table->dropForeign('banknotes_currency_id_foreign');
            $table->dropForeign('banknotes_nominal_value_id_foreign');
            $table->dropForeign('banknotes_series_id_foreign');
            $table->dropForeign('banknotes_time_period_id_foreign');

            // Recreate als RESTRICT/NO ACTION
            $table->foreign('country_id')
                ->references('id')->on('countries')
                ->noActionOnDelete();

            $table->foreign('currency_id')
                ->references('id')->on('currencies')
                ->noActionOnDelete();

            $table->foreign('nominal_value_id')
                ->references('id')->on('nominal_values')
                ->noActionOnDelete();

            $table->foreign('series_id')
                ->references('id')->on('banknote_series')
                ->noActionOnDelete();

            $table->foreign('time_period_id')
                ->references('id')->on('banknote_time_periods')
                ->noActionOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('banknotes', function (Blueprint $table) {
            // Terug naar CASCADE zoals het nu in je schema zit
            $table->dropForeign('banknotes_country_id_foreign');
            $table->dropForeign('banknotes_currency_id_foreign');
            $table->dropForeign('banknotes_nominal_value_id_foreign');
            $table->dropForeign('banknotes_series_id_foreign');
            $table->dropForeign('banknotes_time_period_id_foreign');

            $table->foreign('country_id')
                ->references('id')->on('countries')
                ->cascadeOnDelete();

            $table->foreign('currency_id')
                ->references('id')->on('currencies')
                ->cascadeOnDelete();

            $table->foreign('nominal_value_id')
                ->references('id')->on('nominal_values')
                ->cascadeOnDelete();

            $table->foreign('series_id')
                ->references('id')->on('banknote_series')
                ->cascadeOnDelete();

            $table->foreign('time_period_id')
                ->references('id')->on('banknote_time_periods')
                ->cascadeOnDelete();
        });
    }
};
