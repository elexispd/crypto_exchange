<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            // Change precision to 16,8 (common for cryptocurrencies)
            // This gives you up to 99,999,999.99999999
            $table->decimal('btc_balance', 24, 8)->default(0)->change();
            $table->decimal('eth_balance', 24, 8)->default(0)->change();
            $table->decimal('xrp_balance', 24, 8)->default(0)->change();
            $table->decimal('sol_balance', 24, 8)->default(0)->change();
            $table->decimal('gold_balance', 16, 6)->default(0)->change(); // Less precision for commodities
            $table->decimal('sp500_balance', 16, 6)->default(0)->change();
            $table->decimal('nasdaq_balance', 16, 6)->default(0)->change();
            $table->decimal('oil_balance', 16, 6)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            // Revert to original precision if needed
            $table->decimal('btc_balance', 36, 18)->default(0)->change();
            $table->decimal('eth_balance', 36, 18)->default(0)->change();
            $table->decimal('xrp_balance', 36, 18)->default(0)->change();
            $table->decimal('sol_balance', 36, 18)->default(0)->change();
            $table->decimal('gold_balance', 36, 18)->default(0)->change();
            $table->decimal('sp500_balance', 36, 18)->default(0)->change();
            $table->decimal('nasdaq_balance', 36, 18)->default(0)->change();
            $table->decimal('oil_balance', 36, 18)->default(0)->change();
        });
    }
};
