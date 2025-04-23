<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'from_account', 'to_account', 'amount', 'currency_from', 'currency_to'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
