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
        $currentYear = now()->year;

        // Check if current year is a leap year for precise daily calculation
        $daysInYear = now()->isLeapYear() ? 366 : 365;

        $this->info("Starting profit calculation for {$today} (Year: {$currentYear}, Days: {$daysInYear})...");

        // Get active investments that haven't been redeemed
        $activeInvestments = Invest::where('status', 'active')
            ->whereNull('redeemed_at')
            ->with(['investmentPlan', 'user'])
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

            // Calculate daily profit based on annual rate
            $annualRate = $investment->investmentPlan->interest_rate / 100;
            $dailyRate = $annualRate / $daysInYear;
            $dailyProfit = $investment->amount * $dailyRate;

            // Format for display
            $formattedDailyProfit = number_format($dailyProfit, 2);
            $formattedAnnualRate = number_format($investment->investmentPlan->interest_rate, 2);

            $this->info("Investment {$investment->id}: Amount: {$investment->amount}, Annual Rate: {$formattedAnnualRate}%, Daily Profit: {$formattedDailyProfit}");

            if ($dailyProfit > 0) {
                DB::transaction(function () use ($investment, $dailyProfit, $today, $daysInYear, &$profitCount, &$emailsSent, $formattedDailyProfit) {
                    // Create profit record
                    $profit = InvestmentProfit::create([
                        'invest_id' => $investment->id,
                        'profit_amount' => $dailyProfit,
                        'profit_date' => $today,
                        'credited' => false,
                        'calculation_metadata' => [ // Optional: store calculation details
                            'days_in_year' => $daysInYear,
                            'annual_rate' => $investment->investmentPlan->interest_rate,
                        ]
                    ]);

                    $profitCount++;

                    // Send email notification for interest
                    try {
                        $totalAccumulatedProfit = $investment->totalProfit() + $dailyProfit;

                        Mail::to($investment->user->email)->send(
                            new InvestmentMail(
                                $investment->user,
                                $investment,
                                'interest',
                                $dailyProfit,
                                $totalAccumulatedProfit
                            )
                        );
                        $emailsSent++;
                        $this->info("📧 Email sent for interest: {$formattedDailyProfit} to {$investment->user->email}");
                    } catch (\Exception $e) {
                        $this->error("Failed to send email to {$investment->user->email}: " . $e->getMessage());
                    }
                });
            }
        }

        $this->info("✅ Successfully calculated profits for {$profitCount} investments on {$today}");
        $this->info("📧 {$emailsSent} interest emails sent");
        $this->info("📊 Based on {$daysInYear} days in year {$currentYear}");

        return Command::SUCCESS;
    }
}
