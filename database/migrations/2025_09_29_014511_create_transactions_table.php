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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('wallet_id')->constrained()->onDelete('cascade');

            $table->enum('type', ['deposit', 'withdraw', 'swap']);

            // For deposit/withdraw
            $table->string('currency')->nullable();   // e.g. BTC, ETH
            $table->decimal('amount', 36, 18)->nullable();

            // For swap
            $table->string('from_currency')->nullable();
            $table->string('to_currency')->nullable();
            $table->decimal('from_amount', 36, 18)->nullable();
            $table->decimal('to_amount', 36, 18)->nullable();

            $table->string('status')->default('pending'); // pending, confirmed, failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
