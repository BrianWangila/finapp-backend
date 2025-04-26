<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Card;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'card_id',
        'from_account', 
        'to_account', 
        'amount', 
        'name', 
        'type_of_purchase', 
        'bank_name', 
        'negative', 
        'logo', 
        'date',
        'status',
        'user_id'
    ];

    protected $casts = [
        'date' => 'datetime',
        'negative' => 'boolean',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function card() {
        return $this->belongsTo(Card::class);
    }
}
