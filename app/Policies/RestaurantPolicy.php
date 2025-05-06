<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RestaurantPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Restaurant $restaurant)
    {
        return true; // Anyone can view a restaurant
    }

    public function create(User $user)
    {
        return $user->isRestaurant() || $user->isAdmin();
    }

    public function update(User $user, Restaurant $restaurant)
    {
        return $user->id === $restaurant->user_id || $user->isAdmin();
    }

    public function delete(User $user, Restaurant $restaurant)
    {
        return $user->id === $restaurant->user_id || $user->isAdmin();
    }
}