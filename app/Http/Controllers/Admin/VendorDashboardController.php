<?php


namespace App\Http\Controllers\Admin;


use App\Models\Store;
use App\Models\StoreOrder;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VendorDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // 🔐 Boutique du vendeur
        $store = Store::where('vendor_id', $user->id)->firstOrFail();
        if (!$store) {
            return response()->json([
                'message' => 'Vous n’avez pas de boutique associée.'
            ], 404);
        }
        // 📊 STATS
        $revenue = StoreOrder::where('store_id', $store->id)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $ordersCount = StoreOrder::where('store_id', $store->id)->count();

        $productsCount = Product::where('store_id', $store->id)->count();

        $monthlyEarning = StoreOrder::where('store_id', $store->id)
            ->where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        // 🧾 Dernières commandes
        $latestOrders = StoreOrder::with(['order.user'])
            ->where('store_id', $store->id)
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'customer' => optional($item->order->customer)->first_name,
                    'date' => $item->created_at->format('Y-m-d'),
                    'total' => (float)$item->total_amount,
                    'status' => $item->status,
                    'payment_status' => $item->payment_status,
                ];
            });

        return response()->json([
            'stats' => [
                'revenue' => (float)$revenue,
                'orders' => $ordersCount,
                'products' => $productsCount,
                'monthly_earning' => (float)$monthlyEarning,
            ],
            'latest_orders' => $latestOrders,
        ]);
    }

    public function registerWithStore(Request $request)
    {

        $validated = $request->validate([
            // User
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:6',

            // Store
            'store_name' => 'required|string|max:255',
            'store_phone' => 'nullable|string|max:20',
            'store_description' => 'nullable|string',
            'logo_id' => 'nullable|exists:images,id',
            'cover_id' => 'nullable|exists:images,id',
        ]);

        logger($validated);
        DB::beginTransaction();

        try {

            // 1️⃣ Create user
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'vendor',
                'user_type' => 1,
            ]);

            // 2️⃣ Create store
            $store = Store::create([
                'vendor_id' => $user->id,
                'name' => $validated['store_name'],
                'phone' => $validated['store_phone'] ?? null,
                'description' => $validated['store_description'] ?? null,
                'logo' => $validated['logo_id'] ?? null,
                'cover_image' => $validated['cover_id'] ?? null,
            ]);

            DB::commit();

            // 3️⃣ Create token
            $token = $user->createToken('vendor-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user,
                'store' => $store
            ], 201);

        } catch (\Exception $e) {

            logger($e->getMessage());
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

