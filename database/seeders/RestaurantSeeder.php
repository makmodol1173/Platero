<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get restaurant owner user IDs
        $ownerIds = User::where('role', 'restaurant')->pluck('id')->toArray();

        // Check if we have enough users, if not create them
        if (count($ownerIds) < 3) { // For example, ensure there are at least 3 users (you can adjust this)
            $this->createRestaurantUsers();
            $ownerIds = User::where('role', 'restaurant')->pluck('id')->toArray();
        }

        // Restaurant types for better image search
        $restaurantTypes = [
            'Tasty Bites' => 'international food',
            'Spice Garden' => 'indian food',
            'Ocean Delight' => 'seafood',
            'Pasta Paradise' => 'italian pasta',
            'Burger Barn' => 'gourmet burger',
            'Sushi Supreme' => 'japanese sushi'
        ];

        // Sample restaurant data with cover_image set to null
        $restaurants = [
            [
                'name' => 'Tasty Bites',
                'slug' => 'tasty-bites',
                'email' => 'info@tastybites.com',
                'phone' => '+1-555-123-4567',
                'website' => 'https://tastybites.com',
                'description' => 'Serving delicious international cuisine with a modern twist. Our chefs use only the freshest ingredients to create memorable dining experiences.',
                'address' => '123 Main Street',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'opening_time' => '09:00:00',
                'closing_time' => '22:00:00',
                'user_id' => $ownerIds[0] ?? 1,
                'cover_image' => null,
                'logo' => "restaurants/logos/tasty-bites-logo.jpg",
            ],
            [
                'name' => 'Spice Garden',
                'slug' => 'spice-garden',
                'email' => 'contact@spicegarden.com',
                'phone' => '+1-555-987-6543',
                'website' => 'https://spicegarden.com',
                'description' => 'Authentic Indian cuisine featuring traditional recipes and exotic spices. Experience the rich flavors of India in every bite.',
                'address' => '456 Oak Avenue',
                'city' => 'Chicago',
                'state' => 'IL',
                'zip_code' => '60601',
                'opening_time' => '11:00:00',
                'closing_time' => '23:00:00',
                'user_id' => $ownerIds[1] ?? 1,
                'cover_image' => null,
                'logo' => "restaurants/logos/spice-garden-logo.jpg",
            ],
            [
                'name' => 'Ocean Delight',
                'slug' => 'ocean-delight',
                'email' => 'hello@oceandelight.com',
                'phone' => '+1-555-789-0123',
                'website' => 'https://oceandelight.com',
                'description' => 'Fresh seafood restaurant specializing in sustainable catches from local waters. Our menu changes daily based on what\'s fresh from the sea.',
                'address' => '789 Beach Boulevard',
                'city' => 'Miami',
                'state' => 'FL',
                'zip_code' => '33139',
                'opening_time' => '12:00:00',
                'closing_time' => '22:30:00',
                'user_id' => $ownerIds[2] ?? 1,
                'cover_image' => null,
                'logo' => "restaurants/logos/ocean-delight-logo.jpg",
            ],
            [
                'name' => 'Pasta Paradise',
                'slug' => 'pasta-paradise',
                'email' => 'info@pastaparadise.com',
                'phone' => '+1-555-456-7890',
                'website' => 'https://pastaparadise.com',
                'description' => 'Authentic Italian pasta made fresh daily. Our recipes have been passed down through generations for an authentic taste of Italy.',
                'address' => '321 Vine Street',
                'city' => 'San Francisco',
                'state' => 'CA',
                'zip_code' => '94110',
                'opening_time' => '11:30:00',
                'closing_time' => '21:30:00',
                'user_id' => $ownerIds[0] ?? 1,
                'cover_image' => null,
                'logo' => "restaurants/logos/pasta-paradise-logo.jpg",
            ],
            [
                'name' => 'Burger Barn',
                'slug' => 'burger-barn',
                'email' => 'eat@burgerbarn.com',
                'phone' => '+1-555-234-5678',
                'website' => 'https://burgerbarn.com',
                'description' => 'Gourmet burgers made with 100% grass-fed beef. Our signature sauces and freshly baked buns make our burgers unforgettable.',
                'address' => '567 Patty Lane',
                'city' => 'Austin',
                'state' => 'TX',
                'zip_code' => '78701',
                'opening_time' => '10:00:00',
                'closing_time' => '23:00:00',
                'user_id' => $ownerIds[1] ?? 1,
                'cover_image' => null,
                'logo' => "restaurants/logos/burger-barn-logo.jpg",
            ],
            [
                'name' => 'Sushi Supreme',
                'slug' => 'sushi-supreme',
                'email' => 'hello@sushisupreme.com',
                'phone' => '+1-555-345-6789',
                'website' => 'https://sushisupreme.com',
                'description' => 'Premium sushi and Japanese cuisine prepared by master chefs. We import the freshest fish daily for an authentic experience.',
                'address' => '888 Wasabi Way',
                'city' => 'Seattle',
                'state' => 'WA',
                'zip_code' => '98101',
                'opening_time' => '12:00:00',
                'closing_time' => '22:00:00',
                'user_id' => $ownerIds[2] ?? 1,
                'cover_image' => null,
                'logo' => "restaurants/logos/sushi-supreme-logo.jpg",
            ],
        ];

        // Insert restaurants
        foreach ($restaurants as $restaurantData) {
            Restaurant::firstOrCreate(
                ['email' => $restaurantData['email']],
                $restaurantData
            );
        }

        $this->command->info('Restaurants seeded successfully with cover_image set to null!');
    }

    /**
     * Create default restaurant users if they do not exist.
     */
    private function createRestaurantUsers()
    {
        $users = [
            [
                'name' => 'Restaurant Owner 1',
                'email' => 'owner1@example.com',
                'password' => bcrypt('password'), // Secure password
                'role' => 'restaurant',
            ],
            [
                'name' => 'Restaurant Owner 2',
                'email' => 'owner2@example.com',
                'password' => bcrypt('password'), // Secure password
                'role' => 'restaurant',
            ],
            [
                'name' => 'Restaurant Owner 3',
                'email' => 'owner3@example.com',
                'password' => bcrypt('password'), // Secure password
                'role' => 'restaurant',
            ]
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }
        
        $this->command->info('Default restaurant users created.');
    }
}
