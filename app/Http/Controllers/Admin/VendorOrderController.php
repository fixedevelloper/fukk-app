<?php


namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\StoreOrder;
use App\Http\Resources\StoreOrderWithOrderDateResource;
use Illuminate\Routing\Controller;

class VendorOrderController extends Controller
{
    public function index(Request $request)
    {
        // 1. Récupérer le store de l'utilisateur connecté
        $store = Store::where('vendor_id', auth()->id())->first();

        if (!$store) {
            return response()->json([
                'message' => 'Vous n’avez pas de boutique associée.'
            ], 404);
        }

        // 2. Récupérer les commandes de cette boutique
        $storeOrders = StoreOrder::with(['store', 'order', 'orderProducts','order.customer',])
            ->where('store_id', $store->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20); // pagination facultative

        // 3. Retourner avec le resource
        return StoreOrderWithOrderDateResource::collection($storeOrders);
    }
    public function show($id)
    {
        // 2. Récupérer les commandes de cette boutique
        $storeOrder = StoreOrder::with(['store', 'order', 'orderProducts','order.customer','order.address',])
            ->where('id', $id)
            ->first(); // pagination facultative

        return new StoreOrderWithOrderDateResource($storeOrder);
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);
        $storeOrder=StoreOrder::query()->find($id);
        $storeOrder->update([
            'status' => $request->status
        ]);

        return new StoreOrderWithOrderDateResource(
            $storeOrder->load(['store', 'order', 'order.customer', 'order.address', 'orderProducts'])
        );
    }

}
