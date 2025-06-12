<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Fetch all products with their associated categories
            $products = Product::with('Category')->get();

            // Fetch all categories
            $categories = Category::all();

            // Check if data exists
            if ($products->isEmpty() && $categories->isEmpty()) {
                return response()->json([
                    'message' => 'No products or categories found',
                    'products' => [],
                    'categories' => []
                ], 200);
            }

            return response()->json([
                'message' => 'Data retrieved successfully',
                'products' => $products,
                'categories' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Cart CRUD
    public function cartIndex(Request $request)
    {
        try {
            $user = 1;
            // $user = Auth::user();
            // if (!$user) {
            //     return response()->json(['message' => 'Unauthorized'], 401);
            // }

            $cartItems = Cart::where('user_id', $user)->with('product.category')->get();

            return response()->json([
                'message' => 'Cart retrieved successfully',
                'cart' => $cartItems
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cartStore(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()->first()], 422);
            }

            try {
                // $user = Auth::user();
                // if (!$user) {
                //     return response()->json(['message' => 'Unauthorized'], 401);
                // }
                $userId = $request->user_id; // Temporary, replace with Auth::id() when auth is enabled

                // Check if product exists and has sufficient stock
                $product = Product::findOrFail($request->product_id);
                $existingCartItem = Cart::where('user_id', $userId)
                    ->where('product_id', $request->product_id)
                    ->first();

                if ($existingCartItem) {
                    // Update quantity if item exists
                    $newQuantity = $existingCartItem->quantity + $request->quantity;
                    $existingCartItem->update(['quantity' => $newQuantity]);
                    $cartItem = $existingCartItem;
                } else {
                    $cartItem = Cart::create([
                        'user_id' => $userId,
                        'product_id' => $request->product_id,
                        'quantity' => $request->quantity,
                    ]);
                }

                // Load product relationship for response
                $cartItem->load('product');

                return response()->json([
                    'message' => 'Item added to cart successfully',
                    'cart_item' => $cartItem,
                    'success' => true, // Added for frontend consistency
                ], 201);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'An error occurred while adding to cart',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

    public function cartUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        try {
            // $user = Auth::user();
            // if (!$user) {
            //     return response()->json(['message' => 'Unauthorized'],  に401);
            // }
            $userId = $request->user_id; // Temporary, replace with Auth::id()

            $cartItem = Cart::where('id', $id)->where('user_id', $userId)->first();
            if (!$cartItem) {
                return response()->json(['message' => 'Cart item not found'], 404);
            }

            // Check stock availability
            $product = Product::findOrFail($cartItem->product_id);

            $cartItem->update(['quantity' => $request->quantity]);
            $cartItem->load('product');

            return response()->json([
                'message' => 'Cart item updated successfully',
                'cart_item' => $cartItem,
                'success' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating cart',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function cartDetail($user_id)
    {
        try {
            // Validasi user_id
            if (!is_numeric($user_id) || $user_id <= 0) {
                return response()->json([
                    'message' => 'Invalid user ID',
                ], 400);
            }

            // Gunakan findOrFail untuk efisiensi
            $user = User::findOrFail($user_id);

            // Eager loading untuk relasi (jika ada, misalnya cart items dengan produk)
            $cartItems = Cart::where('user_id', $user_id)
                ->with('product.Category') // Ganti 'product' dengan nama relasi yang sesuai di model Cart
                ->get();

            // Jika keranjang kosong, berikan respons yang jelas
            if ($cartItems->isEmpty()) {
                return response()->json([
                    'message' => 'Cart is empty',
                    'cart' => [],
                ], 200);
            }

            return response()->json([
                'message' => 'Cart details retrieved successfully',
                'cart' => $cartItems,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching cart details',
            ], 500);
        }
    }

    public function cartDestroy($id)
{
    try {
        // $user = Auth::user();
        // if (!$user) {
        //     return response()->json(['message' => 'Unauthorized'], 401);
        // }
        $userId = 1; // Temporary, replace with Auth::id()

        $cartItem = Cart::where('id', $id)->where('user_id', $userId)->first();
        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        $cartItem->delete();

        return response()->json([
            'message' => 'Cart item deleted successfully',
            'success' => true,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred while deleting cart item',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    // Order CRUD
    public function orderIndex(Request $request)
    {
        try {
            // $user = Auth::user();
            $user = 1;
            // if (!$user) {
            //     return response()->json(['message' => 'Unauthorized'], 401);
            // }

            $orders = Order::where('user_id', $user)->with('orderItems.product.Category', 'payment')->get();

            return response()->json([
                'message' => 'Orders retrieved successfully',
                'orders' => $orders
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function orderStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        try {
            $user = 1;
            // $user = Auth::user();
            // if (!$user) {
            //     return response()->json(['message' => 'Unauthorized'], 401);
            // }

            $totalAmount = 0;
            $items = $request->items;

            // Calculate total amount
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    return response()->json(['message' => 'Product not found'], 404);
                }
                $totalAmount += $product->price * $item['quantity'];
            }

            // Create order
            $order = Order::create([
                'user_id' => $user,
                'total_amount' => $totalAmount,
                'status' => 'pending'
            ]);

            // Create order items
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price
                ]);
            }

            // Optionally clear cart after order creation
            Cart::where('user_id', $user)->delete();

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load('orderItems.product.Category')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function orderUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $order = Order::where('id', $id)->where('user_id', $user->id)->first();
            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            $order->update(['status' => $request->status]);

            return response()->json([
                'message' => 'Order updated successfully',
                'order' => $order->load('orderItems.product.Category')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating order',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function orderDestroy($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $order = Order::where('id', $id)->where('user_id', $user->id)->first();
            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            $order->delete();

            return response()->json(['message' => 'Order deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Payment CR
    public function paymentIndex(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $payments = Payment::whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with('order')->get();

            return response()->json([
                'message' => 'Payments retrieved successfully',
                'payments' => $payments
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving payments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function paymentStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer' => 'required|array',
            'customer.id' => 'required|integer',
            'customer.name' => 'required|string',
            'customer.email' => 'required|email',
            'customer.address' => 'required|string',
            'customer.phone' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.product_name' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.subtotal' => 'required|numeric|min:0',
            'items.*.category' => 'required|string',
            'order_summary' => 'required|array',
            'order_summary.total_amount' => 'required|numeric|min:0',
            'order_summary.total_items' => 'required|integer|min:1',
            'order_summary.order_date' => 'required|string',
            'order_summary.order_time' => 'required|string',
            'payment_method' => 'nullable|in:bank_transfer,credit_card,ewallet'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        try {
            $user = 1;
            // $user = Auth::user();
            // if (!$user) {
            //     return response()->json(['message' => 'Unauthorized'], 401);
            // }

            // Generate unique order ID with ORD- prefix
            $orderId = 'ORD-' . strtoupper(uniqid());

            // Ensure uniqueness (check if order_id already exists)
            while (Order::where('order_id', $orderId)->exists()) {
                $orderId = 'ORD-' . strtoupper(uniqid());
            }

            // Create order
            $order = Order::create([
                'order_id' => $orderId,
                'user_id' => $user,
                'alamat' => $request->customer['address'],
                'telepon' => $request->customer['phone'],
                'total_harga' => $request->order_summary['total_amount'],
                'jumlah' => $request->order_summary['total_items'],
                'metode_pembayaran' => '',
                'status' => 'pending'
            ]);

            // Create order items
            $orderItems = [];
            foreach ($request->items as $item) {
                $orderItems[] = [
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['subtotal']
                ];
            }

            // Bulk insert order items for better performance
            OrderItem::insert($orderItems);

            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $request->order_summary['total_amount'],
                'payment_method' => "Qris",
                // 'payment_method' => $request->payment_method,
                'status' => 'pending',
                'transaction_id' => $request->transaction_id ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ]);;

            // Load order with items for response
            $order->load('orderItems');

            return response()->json([
                'message' => 'Order and payment created successfully',
                'order_id' => $orderId,
                'order' => $order,
                'payment' => $payment
            ], 201);
        } catch (\Exception $e) {
            // Rollback transaction on error

            return response()->json([
                'message' => 'An error occurred while creating order and payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Alternative method to generate sequential order ID
    private function generateOrderId()
    {
        // Get the last order number for today
        $today = date('Ymd');
        $lastOrder = Order::where('order_id', 'like', "ORD-{$today}%")
            ->orderBy('order_id', 'desc')
            ->first();

        if ($lastOrder) {
            // Extract the sequence number and increment
            $lastSequence = (int) substr($lastOrder->order_id, -4);
            $newSequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
        } else {
            // First order of the day
            $newSequence = '0001';
        }

        return "ORD-{$today}{$newSequence}";
    }
}