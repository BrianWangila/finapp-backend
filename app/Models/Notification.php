<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'is_read'
    ];


    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}