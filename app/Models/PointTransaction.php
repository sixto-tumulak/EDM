<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'payment_method', 
        'type',
        'payment_receiver', 
        'status',
        'proof',
    ];

    public function user_id()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method');
    }
}