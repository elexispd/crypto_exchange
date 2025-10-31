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
        Schema::table('transaction_fees', function (Blueprint $table) {
            $table->enum('type', ['Deposit', 'Swap', 'Withdrawal'])->default('Deposit');
            $table->string('network')->nullable();
            $table->unique(['type', 'network']); // Prevent duplicate fees for same type+network
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_fees', function (Blueprint $table) {
            $table->dropColumn(['type', 'network']);
            $table->dropUnique(['type', 'network']);
        });
    }
};
