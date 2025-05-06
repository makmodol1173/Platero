<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'amount',
        'payment_method',
        'currency',
        'status',
        'payment_details',
    ];

    protected $casts = [
        'payment_details' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}