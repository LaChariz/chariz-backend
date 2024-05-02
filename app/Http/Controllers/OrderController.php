<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $orders = Order::all();
        return OrderResource::collection($orders);
    }

    public function show($orderId)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $order = Order::findOrFail($orderId);
        return new OrderResource($order);
    }

    public function update(Request $request, $orderId)
    {
        try {
            if (auth()->user()->role !== 'admin') {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $data = $request->validate([
                'status' => 'sometimes|in:ongoing,delivered,cancelled,returned,pending',
            ]);

            $order = Order::findOrFail($orderId);

            $order->update($data);

            return new OrderResource($order->fresh());

        } catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
