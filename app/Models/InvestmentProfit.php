<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class InvestmentProfit extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'invest_id',
        'profit_amount',
        'profit_date',
        'credited'
    ];

    protected $casts = [
        'profit_amount' => 'decimal:8',
        'profit_date' => 'date',
        'credited' => 'boolean'
    ];

    public function invest()
    {
        return $this->belongsTo(Invest::class);
    }
}
