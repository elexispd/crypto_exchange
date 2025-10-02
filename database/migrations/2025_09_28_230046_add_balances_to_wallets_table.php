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
        Schema::table('wallets', function (Blueprint $table) {
            $table->decimal('btc_balance', 36, 18)->default(0);
            $table->decimal('eth_balance', 36, 18)->default(0);
            $table->decimal('xrp_balance', 36, 18)->default(0);
            $table->decimal('sol_balance', 36, 18)->default(0);
            $table->decimal('gold_balance', 36, 18)->default(0);
            $table->decimal('sp500_balance', 36, 18)->default(0);
            $table->decimal('nasdaq_balance', 36, 18)->default(0);
            $table->decimal('oil_balance', 36, 18)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn([
                'btc_balance',
                'eth_balance',
                'xrp_balance',
                'sol_balance',
                'gold_balance',
                'sp500_balance',
                'nasdaq_balance',
                'oil_balance',
            ]);
        });
    }
};
