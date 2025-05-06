<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Order $order)
    {
        // Admin can view any order
        if ($user->isAdmin()) {
            return true;
        }
        
        // Restaurant owner can view orders for their restaurant
        if ($user->isRestaurant() && $user->restaurant && $order->restaurant_id === $user->restaurant->id) {
            return true;
        }
        
        // Customer can view their own orders
        return $user->id === $order->user_id;
    }

    public function update(User $user, Order $order)
    {
        // Admin can update any order
        if ($user->isAdmin()) {
            return true;
        }
        
        // Restaurant owner can update orders for their restaurant
        if ($user->isRestaurant() && $user->restaurant && $order->restaurant_id === $user->restaurant->id) {
            return true;
        }
        
        return false;
    }

    public function pay(User $user, Order $order)
    {
        // Only the customer who placed the order can pay for it
        return $user->id === $order->user_id;
    }
}