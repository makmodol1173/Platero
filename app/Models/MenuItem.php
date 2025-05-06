<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'is_vegetarian',
        'is_vegan',
        'is_gluten_free',
        'is_available',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}