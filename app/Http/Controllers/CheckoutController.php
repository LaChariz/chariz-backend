<?php

namespace App\Http\Controllers;

use App\Models\BillingDetail;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Resources\OrderResource;

class CheckoutController extends Controller
{
    private function validatedDetails(Request $request)
    {
        return $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'phone' => 'required|string',
            'email'  => 'required|string',
            'company_name'  => 'nullable|string',
            'street_address' => 'required|string',
            'town_city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'zip_code' => 'nullable|string',
            'payment_method' => 'required|in:cash_on_delivery,bank_transfer,cheque,card',
            'card_type' => 'required_if:payment_method,card|string',
            'card_number' => 'required_if:payment_method,card|string',
            'expiry' => 'required_if:payment_method,card|string',
            'cvv' => 'required_if:payment_method,card|string',
            'card_name' => 'required_if:payment_method,card|string',
            'payment_status' => 'nullable|in:success,failed',
            'cart_items' => 'required|array',
            'cart_items.*.product_id' => 'required|exists:products,id',
            'cart_items.*.product_price' => 'required|numeric',
            'cart_items.*.quantity' => 'required|integer',
            'total_cost' => 'required|numeric'
        ]);
    }

    private function createOrder($validatedData, $totalCost, $paymentMethod)
    {
        $userId = Auth::check() ? Auth::id() : null;

        $billingDetails = BillingDetail::create(array_merge($validatedData, ['user_id' => $userId]));

        $order = new Order();
        $order->user_id = $userId;
        $order->billing_details_id = $billingDetails->id;
        $order->payment_method = $paymentMethod; 
        $order->total_price = $totalCost;
        $order->order_number = $this->generateOrderNumber();

        if ($paymentMethod === 'card') {
            $order->status = 'ongoing';
        } else {
            $order->status = 'pending';
        }

        $order->save();

        return $order;
    }

    private function generateOrderNumber()
    {
        $orderNumber = rand(100000, 999999);
        while (Order::where('order_number', $orderNumber)->exists()) {
            $orderNumber = rand(100000, 999999);
        }

        return $orderNumber;
    }

    private function associateCartItemsWithOrder($order, $cartItems)
    {
        foreach ($cartItems as $cartItem) {
            $orderItem = new OrderItem([
                'product_id' => $cartItem['product_id'],
                'quantity' => $cartItem['quantity'],
                'price' => $cartItem['product_price'],
            ]);

            $order->orderItems()->save($orderItem);
        }
    }

    public function checkout(Request $request)
    {
        try{

            $validatedData = $this->validatedDetails($request);
        
            $paymentMethod = $validatedData['payment_method'];
        
            $cartItems = $validatedData['cart_items'];

            $totalCost = $validatedData['total_cost'];

            $order = $this->createOrder($validatedData, $totalCost, $paymentMethod);

            $this->associateCartItemsWithOrder($order, $cartItems);

            $orderResource = new OrderResource($order);

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => $orderResource,
            ], 200);

        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
