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
        'status'
    ];

    public function investmentPlan()
    {
        return $this->belongsTo(InvestmentPlan::class, 'investment_plan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
