<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\MenuItem;
use App\Models\DeliveryInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (auth()->user()->isAdmin()) {
            $orders = Order::with('restaurant', 'user')->latest()->paginate(20);
        } elseif (auth()->user()->isRestaurant()) {
            $restaurant = auth()->user()->restaurant;
            $orders = Order::where('restaurant_id', $restaurant->id)
                ->with('user')
                ->latest()
                ->paginate(20);
        } else {
            $orders = Order::where('user_id', auth()->id())
                ->with('restaurant')
                ->latest()
                ->paginate(20);
        }

        return view('orders.index', compact('orders'));
    }

    public function create(Restaurant $restaurant)
    {
        $menus = $restaurant->menus()->where('is_active', true)->with(['menuItems' => function($query) {
            $query->where('is_available', true);
        }])->get();
        
        return view('orders.create', compact('restaurant', 'menus'));
    }

    public function store(Request $request, Restaurant $restaurant)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.special_instructions' => 'nullable|string',
            'recipient_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string',
        ]);

        // Calculate order totals
        $subtotal = 0;
        $items = [];

        foreach ($validated['items'] as $item) {
            $menuItem = MenuItem::findOrFail($item['menu_item_id']);
            $itemSubtotal = $menuItem->price * $item['quantity'];
            $subtotal += $itemSubtotal;

            $items[] = [
                'menu_item_id' => $menuItem->id,
                'quantity' => $item['quantity'],
                'unit_price' => $menuItem->price,
                'subtotal' => $itemSubtotal,
                'special_instructions' => $item['special_instructions'] ?? null,
            ];
        }

        // Apply tax and delivery fee
        $tax = $subtotal * 0.05; // 5% tax
        $deliveryFee = 50; // Fixed delivery fee in BDT
        $total = $subtotal + $tax + $deliveryFee;

        // Create order
        $order = Order::create([
            'user_id' => auth()->id(),
            'restaurant_id' => $restaurant->id,
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Create order items
        foreach ($items as $item) {
            $order->orderItems()->create($item);
        }

        // Create delivery info
        $order->deliveryInfo()->create([
            'recipient_name' => $validated['recipient_name'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'state' => $validated['state'] ?? null,
            'zip_code' => $validated['zip_code'],
            'phone' => $validated['phone'],
        ]);

        return redirect()->route('orders.payment', $order);
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        
        $order->load('restaurant', 'orderItems.menuItem', 'deliveryInfo', 'paymentTransaction');
        
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,out_for_delivery,delivered,cancelled',
        ]);

        $order->update([
            'status' => $validated['status'],
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order status updated successfully!');
    }
}