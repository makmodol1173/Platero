<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Add this import

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure storage directory exists
        Storage::disk('public')->makeDirectory('menu-items');
        
        // Get all menus
        $menus = Menu::all();
        
        if ($menus->isEmpty()) {
            $this->command->error('No menus found. Please run MenuSeeder first or create menus manually.');
            return;
        }
        
        // Menu items by category
        $menuItemsByCategory = [
            // Breakfast items
            'Breakfast' => [
                ['name' => 'Eggs Benedict', 'description' => 'Poached eggs on English muffin with hollandaise sauce', 'price' => 12.99],
                ['name' => 'Pancake Stack', 'description' => 'Fluffy pancakes with maple syrup and butter', 'price' => 9.99],
                ['name' => 'Avocado Toast', 'description' => 'Smashed avocado on artisan bread with poached egg', 'price' => 11.99],
                ['name' => 'Breakfast Burrito', 'description' => 'Scrambled eggs, cheese, and bacon wrapped in a tortilla', 'price' => 10.99],
                ['name' => 'French Toast', 'description' => 'Brioche bread dipped in egg batter and grilled to perfection', 'price' => 9.99],
                ['name' => 'Omelette', 'description' => 'Three-egg omelette with your choice of fillings', 'price' => 12.99],
            ],
            
            // Lunch items
            'Lunch' => [
                ['name' => 'Caesar Salad', 'description' => 'Romaine lettuce with Caesar dressing, croutons, and parmesan', 'price' => 10.99],
                ['name' => 'Club Sandwich', 'description' => 'Triple-decker sandwich with turkey, bacon, lettuce, and tomato', 'price' => 12.99],
                ['name' => 'Chicken Wrap', 'description' => 'Grilled chicken with vegetables in a flour tortilla', 'price' => 11.99],
                ['name' => 'Soup of the Day', 'description' => 'Freshly made soup served with artisan bread', 'price' => 8.99],
                ['name' => 'Burger', 'description' => 'Beef patty with lettuce, tomato, and special sauce on a brioche bun', 'price' => 13.99],
                ['name' => 'Pasta Salad', 'description' => 'Pasta with vegetables and Italian dressing', 'price' => 9.99],
            ],
            
            // Default items for any other category
            'default' => [
                ['name' => 'House Special 1', 'description' => 'Chef\'s special creation', 'price' => 14.99],
                ['name' => 'House Special 2', 'description' => 'Seasonal specialty', 'price' => 16.99],
                ['name' => 'House Special 3', 'description' => 'Customer favorite', 'price' => 15.99],
                ['name' => 'House Special 4', 'description' => 'Traditional recipe', 'price' => 13.99],
                ['name' => 'House Special 5', 'description' => 'Signature dish', 'price' => 17.99],
                ['name' => 'House Special 6', 'description' => 'Premium selection', 'price' => 19.99],
            ],
        ];
        
        $totalItems = 0;
        $errorCount = 0;
        
        // Start a database transaction
        DB::beginTransaction();
        
        try {
            foreach ($menus as $menu) {
                // Get items for this menu category
                $categoryName = $menu->name;
                $items = $menuItemsByCategory[$categoryName] ?? $menuItemsByCategory['default'];
                
                // Get restaurant info for better image search and unique slugs
                $restaurantName = $menu->restaurant->name ?? 'Restaurant';
                $restaurantSlug = $menu->restaurant->slug ?? Str::slug($restaurantName);
                
                foreach ($items as $index => $itemData) {
                    try {
                        // Create a unique slug for the item that includes the menu ID and restaurant
                        $baseSlug = Str::slug($itemData['name']);
                        $uniqueSlug = "{$restaurantSlug}-{$menu->id}-{$baseSlug}";
                        
                        // Generate a placeholder image path
                        $imagePath = "menu-items/{$restaurantSlug}-{$menu->id}-{$baseSlug}.jpg";
                        
                        // Create a simple placeholder image if it doesn't exist
                        if (!Storage::disk('public')->exists($imagePath)) {
                            // Create a simple colored rectangle as a placeholder
                            $width = 800;
                            $height = 600;
                            $image = imagecreatetruecolor($width, $height);
                            
                            // Generate a random color based on the item name
                            $hash = md5($itemData['name'] . $menu->id);
                            $r = hexdec(substr($hash, 0, 2));
                            $g = hexdec(substr($hash, 2, 2));
                            $b = hexdec(substr($hash, 4, 2));
                            
                            $color = imagecolorallocate($image, $r, $g, $b);
                            $textColor = imagecolorallocate($image, 255, 255, 255);
                            
                            // Fill the background
                            imagefill($image, 0, 0, $color);
                            
                            // Add text
                            $text = $itemData['name'];
                            $fontSize = 5;
                            $fontWidth = imagefontwidth($fontSize);
                            $fontHeight = imagefontheight($fontSize);
                            $textWidth = $fontWidth * strlen($text);
                            $textX = ($width - $textWidth) / 2;
                            $textY = ($height - $fontHeight) / 2;
                            
                            imagestring($image, $fontSize, $textX, $textY, $text, $textColor);
                            
                            // Save the image
                            ob_start();
                            imagejpeg($image);
                            $imageData = ob_get_clean();
                            Storage::disk('public')->put($imagePath, $imageData);
                            
                            // Free up memory
                            imagedestroy($image);
                        }
                        
                        // Check if the menu item already exists
                        $existingItem = MenuItem::where('menu_id', $menu->id)
                            ->where('name', $itemData['name'])
                            ->first();
                        
                        if ($existingItem) {
                            // Update existing item
                            $existingItem->description = $itemData['description'];
                            $existingItem->price = $itemData['price'];
                            $existingItem->image = $imagePath;
                            $existingItem->is_available = true;
                            $existingItem->slug = $uniqueSlug;
                            $existingItem->save();
                            
                            $this->command->info("Updated menu item: {$itemData['name']} for menu {$menu->id}");
                        } else {
                            // Insert new item with all required fields
                            $menuItem = new MenuItem();
                            $menuItem->menu_id = $menu->id;
                            $menuItem->restaurant_id = $menu->restaurant_id; // ✅ Fix: Set restaurant_id
                            $menuItem->name = $itemData['name'];
                            $menuItem->slug = $uniqueSlug; // Use the unique slug
                            $menuItem->description = $itemData['description'];
                            $menuItem->price = $itemData['price'];
                            $menuItem->image = $imagePath;
                            $menuItem->is_available = true;
                                                    
                            // Optional fields
                            if (Schema::hasColumn('menu_items', 'is_vegetarian')) {
                                $menuItem->is_vegetarian = rand(0, 5) === 0; // 20% chance
                            }
                            
                            if (Schema::hasColumn('menu_items', 'is_spicy')) {
                                $menuItem->is_spicy = rand(0, 5) === 0;
                            }
                            
                            if (Schema::hasColumn('menu_items', 'sort_order')) {
                                $menuItem->sort_order = $index + 1;
                            }
                            
                            $menuItem->save();
                            $this->command->info("Created menu item: {$itemData['name']} for menu {$menu->id}");

                        }
                        
                        $totalItems++;
                        
                    } catch (\Exception $e) {
                        $this->command->error("Error with menu item {$itemData['name']} for menu {$menu->id}: " . $e->getMessage());
                        $errorCount++;
                    }
                }
            }
            
            // Commit the transaction
            DB::commit();
            
            $this->command->info("Menu items seeded successfully! Created/updated {$totalItems} items with {$errorCount} errors.");
            
        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();
            $this->command->error("Fatal error: " . $e->getMessage());
        }
    }
}