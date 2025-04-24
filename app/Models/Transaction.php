<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'from_account', 'to_account', 'amount', 'name', 'type_of_purchase', 'bank_name', 'negative', 'logo', 'date'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
