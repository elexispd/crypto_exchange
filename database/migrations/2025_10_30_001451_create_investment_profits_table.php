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
        Schema::create('investment_profits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('invest_id')->constrained('invests')->onDelete('cascade');
            $table->decimal('profit_amount', 16, 8);
            $table->date('profit_date');
            $table->boolean('credited')->default(false);
            $table->timestamps();

            $table->index(['invest_id', 'profit_date']);
            $table->index(['profit_date', 'credited']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_profits');
    }
};
