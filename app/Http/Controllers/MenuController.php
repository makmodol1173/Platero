<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:restaurant,admin');
    }

    public function index(Restaurant $restaurant)
    {
        $this->authorize('view', $restaurant);
        $menus = $restaurant->menus()->paginate(10);
        return view('menus.index', compact('restaurant', 'menus'));
    }

    public function create(Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        return view('menus.create', compact('restaurant'));
    }

    public function store(Request $request, Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['restaurant_id'] = $restaurant->id;
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);
        $validated['is_active'] = $request->has('is_active');

        $menu = Menu::create($validated);

        return redirect()->route('restaurants.menus.show', [$restaurant, $menu])
            ->with('success', 'Menu created successfully!');
    }

    public function show(Restaurant $restaurant, Menu $menu)
    {
        $this->authorize('view', $restaurant);
        $menuItems = $menu->menuItems()->paginate(12);
        return view('menus.show', compact('restaurant', 'menu', 'menuItems'));
    }

    public function edit(Restaurant $restaurant, Menu $menu)
    {
        $this->authorize('update', $restaurant);
        return view('menus.edit', compact('restaurant', 'menu'));
    }

    public function update(Request $request, Restaurant $restaurant, Menu $menu)
    {
        $this->authorize('update', $restaurant);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $menu->update($validated);

        return redirect()->route('restaurants.menus.show', [$restaurant, $menu])
            ->with('success', 'Menu updated successfully!');
    }

    public function destroy(Restaurant $restaurant, Menu $menu)
    {
        $this->authorize('update', $restaurant);
        $menu->delete();
        
        return redirect()->route('restaurants.menus.index', $restaurant)
            ->with('success', 'Menu deleted successfully!');
    }
}