<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class InvestmentPlan extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',  'name', 'interest_rate', 'min_amount', 'status'
    ];

    public function invests()
    {
        return $this->hasMany(Invest::class, 'investment_plan_id');
    }
}
