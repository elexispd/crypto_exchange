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
        Schema::table('invests', function (Blueprint $table) {
            $table->timestamp('invested_at')->nullable()->after('status');
            $table->timestamp('redeemed_at')->nullable()->after('invested_at');
            $table->integer('lock_period')->default(30)->after('redeemed_at'); // in days

            // Add index for better performance
            $table->index(['status', 'redeemed_at']);
            $table->index('invested_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invests', function (Blueprint $table) {
            $table->dropColumn(['invested_at', 'redeemed_at', 'lock_period']);
            $table->dropIndex(['status', 'redeemed_at']);
            $table->dropIndex(['invested_at']);
        });
    }
};
