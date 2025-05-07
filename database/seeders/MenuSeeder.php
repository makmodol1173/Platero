<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\Menu;
use Illuminate\Support\Str;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all restaurants
        $restaurants = Restaurant::all();
        
        // Menu categories for each restaurant type
        $menuCategories = [
            'Tasty Bites' => ['Breakfast', 'Lunch', 'Dinner', 'Desserts'],
            'Spice Garden' => ['Appetizers', 'Main Courses', 'Breads', 'Desserts'],
            'Ocean Delight' => ['Starters', 'Fish', 'Shellfish', 'Sides'],
            'Pasta Paradise' => ['Antipasti', 'Pasta', 'Pizza', 'Dolci'],
            'Burger Barn' => ['Burgers', 'Sides', 'Shakes', 'Kids Menu'],
            'Sushi Supreme' => ['Sashimi', 'Maki Rolls', 'Nigiri', 'Hot Dishes']
        ];
        
        // Menu descriptions
        $menuDescriptions = [
            'Breakfast' => 'Start your day with our delicious breakfast options, available until 11:30 AM.',
            'Lunch' => 'Perfect meals for a midday break, available from 11:30 AM to 4:00 PM.',
            'Dinner' => 'Evening specialties crafted by our executive chef, available after 4:00 PM.',
            'Desserts' => 'Sweet treats to end your meal on a perfect note.',
            'Appetizers' => 'Small dishes to stimulate your appetite before the main course.',
            'Main Courses' => 'Hearty dishes that showcase our chef\'s expertise and creativity.',
            'Breads' => 'Freshly baked breads to complement your meal.',
            'Starters' => 'Begin your seafood journey with these delightful small plates.',
            'Fish' => 'Sustainably caught fish prepared with care and expertise.',
            'Shellfish' => 'The finest selection of lobster, crab, shrimp, and more.',
            'Sides' => 'Perfect accompaniments to complete your meal.',
            'Antipasti' => 'Traditional Italian appetizers to start your meal.',
            'Pasta' => 'Handmade pasta dishes with authentic Italian sauces.',
            'Pizza' => 'Wood-fired pizzas with traditional and creative toppings.',
            'Dolci' => 'Italian desserts made with love and tradition.',
            'Burgers' => 'Gourmet burgers made with 100% grass-fed beef and premium toppings.',
            'Shakes' => 'Hand-spun milkshakes made with premium ice cream.',
            'Kids Menu' => 'Kid-friendly options that are both delicious and nutritious.',
            'Sashimi' => 'The freshest cuts of raw fish, served with wasabi and soy sauce.',
            'Maki Rolls' => 'Creative sushi rolls with a variety of fillings.',
            'Nigiri' => 'Hand-pressed sushi with fish atop seasoned rice.',
            'Hot Dishes' => 'Cooked Japanese specialties to warm your soul.'
        ];
        
        foreach ($restaurants as $restaurant) {
            // Get menu categories for this restaurant
            $categories = $menuCategories[$restaurant->name] ?? ['Starters', 'Main Courses', 'Desserts', 'Drinks'];
            
            foreach ($categories as $index => $category) {
                // Generate a slug for the menu
                $slug = Str::slug($restaurant->name . '-' . $category);
                
                Menu::firstOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'name' => $category
                    ],
                    [
                        'restaurant_id' => $restaurant->id,
                        'name' => $category,
                        'slug' => $slug, // Add slug
                        'description' => $menuDescriptions[$category] ?? "Our selection of {$category} features the best ingredients available.",
                        'is_active' => true,
                    ]
                );
            }
        }
        
        $this->command->info('Menus seeded successfully!');
    }
}