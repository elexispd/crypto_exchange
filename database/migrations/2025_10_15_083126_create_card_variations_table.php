<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('card_variations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('color');
            $table->string('funding_type');
            $table->double('min_balance');
            $table->double('daily_cap');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_variations');
    }
};
