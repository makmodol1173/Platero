<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'recipient_name',
        'address',
        'city',
        'state',
        'zip_code',
        'phone',
        'latitude',
        'longitude',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}