<?php

namespace App\Http\Controllers\Front;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\StoreOrder;
use App\Models\OrderProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Passer une commande (multi-boutiques)
     * @param Request $request
     * @return JsonResponse
     */
    public function placeOrder(Request $request)
    {
        logger($request->all());
        $user = $request->user();

        try {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.store_id'   => 'required|exists:stores,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.price'      => 'required|numeric|min:0',
            'address'            => 'required|array', // c’est un objet JSON
            'address.name'       => 'required|string',
            'address.email'      => 'required|email',
            'address.phone'      => 'required|string',
            'address.address'    => 'required|string',
            'address.city'       => 'required|string',
            'address.note'       => 'nullable|string',
        ]);


        DB::beginTransaction();


            /** 1️⃣ Commande globale */
            $order = Order::create([
                'user_id'        => $user->id,
                'total_amount'   => 0,
                'status'         => 'pending',
                'payment_status' => 'pending',
                'sub_total'=>0.0,
                'address'        => json_encode($data['address']), // stock JSON
            ]);

            /** 2️⃣ Grouper par boutique */
            $itemsByStore = collect($data['items'])->groupBy('store_id');

            $globalTotal = 0;

            foreach ($itemsByStore as $storeId => $items) {

                /** 3️⃣ Sous-commande boutique */
                $storeOrder = StoreOrder::create([
                    'order_id'      => $order->id,
                    'store_id'      => $storeId,
                    'total_amount'  => 0,
                    'status'        => 'pending',
                    'payment_status'=> 'pending',
                ]);

                $storeTotal = 0;

                /** 4️⃣ Produits */
                foreach ($items as $item) {
                    $lineTotal = $item['price'] * $item['quantity'];
                    $storeTotal += $lineTotal;

                    OrderProduct::create([
                        'store_order_id' => $storeOrder->id,
                        'product_id'     => $item['product_id'],
                        'qty'       => $item['quantity'],
                        'price'          => $item['price'],
                        'total'          => $lineTotal,
                        'options'        => $item['options'] ?? null,
                        'tax_amount'=>0,
                        'product_name'=>'',
                        'restock_quantity'=>0,
                    ]);
                }

                $storeOrder->update([
                    'total_amount' => $storeTotal,
                ]);

                $globalTotal += $storeTotal;
            }

            /** 5️⃣ Total global */
            $order->update([
                'total_amount' => $globalTotal,
            ]);

            DB::commit();

            return response()->json([
                'success'  => true,
                'order_id' => $order->id,
                'total'    => $globalTotal,
                'message'  => 'Commande passée avec succès',
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            logger()->error('Order error', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la commande',
            ], 500);
        }
    }


    /**
     * Lister les commandes du client connecté
     */
    public function myOrders()
    {
        $orders = Order::with([
            'user',
            'address',
            'storeOrders.store',
            'storeOrders.orderProducts.product',
            'storeOrders.orderProducts.product.featuredImage'
        ])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return OrderResource::collection($orders);
    }

    /**
     * Lister les ventes pour une boutique (store)
     * @param $storeId
     * @return JsonResponse
     */
    public function storeSales($storeId)
    {
        $storeOrders = StoreOrder::with('order.user', 'orderProducts.product')
            ->where('store_id', $storeId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($storeOrders);
    }
}
