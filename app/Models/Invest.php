<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Invest extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'investment_plan_id',
        'network',
        'amount',
        'status',
        'invested_at',
        'redeemed_at',
        'lock_period' // in days
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'invested_at' => 'datetime',
        'redeemed_at' => 'datetime',
    ];

    public function investmentPlan()
    {
        return $this->belongsTo(InvestmentPlan::class, 'investment_plan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profits()
    {
        return $this->hasMany(InvestmentProfit::class, 'invest_id');
    }

    public function canBeRedeemed()
    {
        if ($this->redeemed_at) {
            return false; // Already redeemed
        }

        $lockEndDate = $this->invested_at->addDays(30);
        return now()->greaterThanOrEqualTo($lockEndDate);
    }

     // FIXED: These should return calculated values, not relationships
    public function totalProfit()
    {
        return (float) $this->profits->sum('profit_amount');
    }

    public function creditedProfit()
    {
        return (float) $this->profits->where('credited', true)->sum('profit_amount');
    }

    public function pendingProfit()
    {
        return (float) $this->profits->where('credited', false)->sum('profit_amount');
    }

    // Add accessor for easy access in blades
    public function getTotalProfitAttribute()
    {
        return $this->totalProfit();
    }

    public function getCreditedProfitAttribute()
    {
        return $this->creditedProfit();
    }

    public function getPendingProfitAttribute()
    {
        return $this->pendingProfit();
    }
}
