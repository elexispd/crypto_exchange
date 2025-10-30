<?php

namespace App\Console\Commands;

use App\Models\Invest;
use App\Models\InvestmentProfit;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvestmentMail;

class CalculateInvestmentProfits extends Command
{
    protected $signature = 'investments:calculate-profits';
    protected $description = 'Calculate daily profits for active investments';

    public function handle()
    {
        $today = now()->toDateString();

        $this->info("Starting profit calculation for {$today}...");

        // Get active investments that haven't been redeemed
        $activeInvestments = Invest::where('status', 'active')
            ->whereNull('redeemed_at')
            ->with(['investmentPlan', 'user']) // Add user relationship
            ->get();

        $profitCount = 0;
        $emailsSent = 0;

        foreach ($activeInvestments as $investment) {
            // Check if profit already calculated for today
            $existingProfit = InvestmentProfit::where('invest_id', $investment->id)
                ->where('profit_date', $today)
                ->first();

            if ($existingProfit) {
                $this->line("Profit already calculated for investment {$investment->id} today");
                continue;
            }

            // Calculate daily profit
            $dailyRate = $investment->investmentPlan->interest_rate / 100 / 365;
            $dailyProfit = $investment->amount * $dailyRate;

            if ($dailyProfit > 0) {
                DB::transaction(function () use ($investment, $dailyProfit, $today, &$profitCount, &$emailsSent) {
                    // Create profit record
                    $profit = InvestmentProfit::create([
                        'invest_id' => $investment->id,
                        'profit_amount' => $dailyProfit,
                        'profit_date' => $today,
                        'credited' => false
                    ]);

                    $profitCount++;

                    // Send email notification for interest
                    try {
                        Mail::to($investment->user->email)->send(
                            new InvestmentMail(
                                $investment->user,
                                $investment,
                                'interest',
                                $dailyProfit,
                                $investment->totalProfit() + $dailyProfit // total accumulated profit
                            )
                        );
                        $emailsSent++;
                        $this->info("📧 Email sent for interest: {$dailyProfit} to {$investment->user->email}");
                    } catch (\Exception $e) {
                        $this->error("Failed to send email to {$investment->user->email}: " . $e->getMessage());
                    }
                });
            }
        }

        $this->info("✅ Successfully calculated profits for {$profitCount} investments on {$today}");
        $this->info("📧 {$emailsSent} interest emails sent");

        return Command::SUCCESS;
    }
}
