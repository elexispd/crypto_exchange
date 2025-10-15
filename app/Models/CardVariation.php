<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CardVariation extends Model
{
    use HasUuids;
    protected $table = 'card_variations';

    protected $fillable = [
        'color',
        'funding_type',
        'min_balance',
        'daily_cap',
    ];

    public function cards()
    {
        return $this->hasMany(Card::class);
    }





}
