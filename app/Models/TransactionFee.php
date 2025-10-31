<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionFee extends Model
{
    protected $fillable = [
        'amount', 'status', 'network', 'type'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // You can add scopes for easy querying
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByNetwork($query, $network)
    {
        return $query->where('network', $network);
    }
}
