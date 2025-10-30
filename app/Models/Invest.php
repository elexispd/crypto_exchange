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

        $lockEndDate = $this->invested_at->addDays($this->lock_period ?? 30);
        return now()->greaterThanOrEqualTo($lockEndDate);
    }

    public function totalProfit()
    {
        return $this->profits()->sum('profit_amount');
    }

    public function creditedProfit()
    {
        return $this->profits()->where('credited', true)->sum('profit_amount');
    }

    public function pendingProfit()
    {
        return $this->profits()->where('credited', false)->sum('profit_amount');
    }
}
