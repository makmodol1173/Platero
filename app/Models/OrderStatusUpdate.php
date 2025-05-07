<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusUpdate extends Model
{
    protected $fillable = [
        'order_id', 'status', 'comment', 'created_at', 'updated_at'
    ];
}
