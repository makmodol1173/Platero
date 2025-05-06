<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Restaurant;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }
    
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalRestaurants = Restaurant::count();
        $totalOrders = Order::count();
        $recentOrders = Order::with('restaurant', 'user')->latest()->take(5)->get();
        
        $orderStats = [
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'preparing' => Order::where('status', 'preparing')->count(),
            'out_for_delivery' => Order::where('status', 'out_for_delivery')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];
        
        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalRestaurants', 
            'totalOrders', 
            'recentOrders', 
            'orderStats'
        ));
    }
    
    // User Management
    public function users()
    {
        $users = User::paginate(20);
        return view('admin.users.index', compact('users'));
    }
    
    public function createUser()
    {
        return view('admin.users.create');
    }
    
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,restaurant,customer',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);
        
        return redirect()->route('admin.users')
            ->with('success', 'User created successfully!');
    }
    
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }
    
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,restaurant,customer',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);
        
        $user->update($validated);
        
        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully!');
    }
    
    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')
                ->with('error', 'You cannot delete your own account!');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully!');
    }
    
    // Restaurant Management
    public function restaurants()
    {
        $restaurants = Restaurant::with('user')->paginate(20);
        return view('admin.restaurants.index', compact('restaurants'));
    }
    
    // Order Management
    public function orders()
    {
        $orders = Order::with('restaurant', 'user')->latest()->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }
    
    // System Settings
    public function settings()
    {
        return view('admin.settings');
    }
    
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'delivery_fee' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
        ]);
        
        foreach ($validated as $key => $value) {
            setting([$key => $value]);
        }
        
        return redirect()->route('admin.settings')
            ->with('success', 'Settings updated successfully!');
    }
}