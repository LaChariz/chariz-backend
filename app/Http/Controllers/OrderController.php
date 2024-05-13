<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
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

    public function getUserOrders($userId)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::findOrFail($userId);

        $orders = $user->orders;

        return OrderResource::collection($orders);
    }

    public function getSingleOrder($orderId)
    {
        $user = auth()->user();
        $order = Order::findOrFail($orderId);
        
        if ($order->user_id === $user->id || $user->isAdmin()) {
            return OrderResource::make($order);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function deleteOrder($orderId)
    {

        $order = Order::findOrFail($orderId);
        $user = auth()->user();

        if ($user->isAdmin()) {
            $order->delete();
            return response()->json(['message' => 'Order deleted successfully',], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
