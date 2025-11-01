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
        'id',  'name', 'network', 'interest_rate', 'min_amount', 'status'
    ];

    public function invests()
    {
        return $this->hasMany(Invest::class, 'investment_plan_id');
    }

    public function getNetworkIcon($network)
    {
        $icons = [
            'btc' => 'fab fa-bitcoin text-warning',
            'eth' => 'fab fa-ethereum text-primary',
            'xrp' => 'fas fa-circle text-info',
            'sol' => 'fas fa-sun text-warning',
            'gold' => 'fas fa-gem text-warning',
            'oil' => 'fas fa-gas-pump text-dark',
            'sp500' => 'fas fa-chart-line text-success',
            'nasdaq' => 'fas fa-chart-bar text-info',
        ];

        return $icons[$network] ?? 'fas fa-coins text-secondary';
    }
}
