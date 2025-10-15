<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Card extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'user_id',
        'card_variation_id',
        'card_number',
        'card_name',
        'cvv',
        'expiry_month',
        'expiry_year',
        'is_frozen',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function variation()
    {
        return $this->belongsTo(CardVariation::class, 'card_variation_id');
    }
}
