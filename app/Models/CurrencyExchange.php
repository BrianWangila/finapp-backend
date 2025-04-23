<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyExchange extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'from_currency',
        'to_currency',
        'amount',
        'converted_amount'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
