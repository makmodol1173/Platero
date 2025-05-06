<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:restaurant,admin');
    }

    public function create(Restaurant $restaurant, Menu $menu)
    {
        $this->authorize('update', $restaurant);
        return view('menu-items.create', compact('restaurant', 'menu'));
    }

    public function store(Request $request, Restaurant $restaurant, Menu $menu)
    {
        $this->authorize('update', $restaurant);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_vegetarian' => 'boolean',
            'is_vegan' => 'boolean',
            'is_gluten_free' => 'boolean',
            'is_available' => 'boolean',
        ]);

        $validated['menu_id'] = $menu->id;
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);
        $validated['is_vegetarian'] = $request->has('is_vegetarian');
        $validated['is_vegan'] = $request->has('is_vegan');
        $validated['is_gluten_free'] = $request->has('is_gluten_free');
        $validated['is_available'] = $request->has('is_available');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        $menuItem = MenuItem::create($validated);

        return redirect()->route('restaurants.menus.show', [$restaurant, $menu])
            ->with('success', 'Menu item created successfully!');
    }

    public function edit(Restaurant $restaurant, Menu $menu, MenuItem $menuItem)
    {
        $this->authorize('update', $restaurant);
        return view('menu-items.edit', compact('restaurant', 'menu', 'menuItem'));
    }

    public function update(Request $request, Restaurant $restaurant, Menu $menu, MenuItem $menuItem)
    {
        $this->authorize('update', $restaurant);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_vegetarian' => 'boolean',
            'is_vegan' => 'boolean',
            'is_gluten_free' => 'boolean',
            'is_available' => 'boolean',
        ]);

        $validated['is_vegetarian'] = $request->has('is_vegetarian');
        $validated['is_vegan'] = $request->has('is_vegan');
        $validated['is_gluten_free'] = $request->has('is_gluten_free');
        $validated['is_available'] = $request->has('is_available');

        if ($request->hasFile('image')) {
            if ($menuItem->image) {
                Storage::disk('public')->delete($menuItem->image);
            }
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        $menuItem->update($validated);

        return redirect()->route('restaurants.menus.show', [$restaurant, $menu])
            ->with('success', 'Menu item updated successfully!');
    }

    public function destroy(Restaurant $restaurant, Menu $menu, MenuItem $menuItem)
    {
        $this->authorize('update', $restaurant);
        
        if ($menuItem->image) {
            Storage::disk('public')->delete($menuItem->image);
        }
        
        $menuItem->delete();
        
        return redirect()->route('restaurants.menus.show', [$restaurant, $menu])
            ->with('success', 'Menu item deleted successfully!');
    }
}