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
            'payment_status' => 'required|in:success,failed'
        ]);
    }

    private function createOrder($validatedData, $totalCost, $paymentMethod)
    {
        $userId = Auth::id();

        $billingDetails = BillingDetail::create(array_merge($validatedData, ['user_id' => $userId]));

        $order = new Order();
        $order->user_id = $userId;
        $order->billing_details_id = $billingDetails->id;
        $order->payment_method = $paymentMethod; 
        $order->total_price = $totalCost;

        if ($paymentMethod === 'card') {
            $order->status = 'ongoing';
        } else {
            $order->status = 'pending';
        }

        $order->save();

        return $order;
    }

    private function associateCartItemsWithOrder($order, $cart)
    {
        foreach ($cart->cartItems as $cartItem) {
            // Create a new order item and associate it with the order
            $orderItem = new OrderItem([
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->product->price,
            ]);

            // Associate the order item with the order
            $order->orderItems()->save($orderItem);
        }
    }

    private function clearCart($cart)
    {
        $cart->cartItems()->delete();
        $cart->delete();
    }

    public function checkout(Request $request)
    {
        try{

            // Step 1: Validate billing details
            $validatedData = $this->validatedDetails($request);
        
            // Step 2: Determine payment method
            $paymentMethod = $validatedData['payment_method'];
        
            // Step 3: Process payment if necessary (for online payment methods)
            if ($paymentMethod === 'card') {
                // Assuming the frontend sends a 'payment_status' indicating success or failure
                if (!$request->has('payment_status') || $request->input('payment_status') !== 'success') {
                    return response()->json(['error' => 'Payment failed'], 400);
                }
            }
            
            // Step 4: Create or retrieve the user's cart
            $cart = null;
            if (Auth::check()) {
                $user = Auth::user();
                $cart = $user->cart;
            } else {
                $cartId = Session::get('cart_id');
                if (!$cartId) {
                    return response()->json(['error' => 'Cart is empty'], 404);
                }
                $cart = Cart::with('cartItems.product')->find($cartId);
            }

            // Step 5: Calculate total cost
            $totalCost = 0;
            if ($cart !== null && $cart->cartItems !== null) {
                foreach ($cart->cartItems as $cartItem) {
                    $totalCost += $cartItem->product->price * $cartItem->quantity;
                }
            } else {
                return response()->json(['error' => 'Cart is empty'], 404);
            }

            // Step 6: Create order
            $order = $this->createOrder($validatedData, $totalCost, $paymentMethod);

            // Step 7: Associate cart items with the order
            $this->associateCartItemsWithOrder($order, $cart);

            // Step 8: Clear cart
            $this->clearCart($cart);
            Session::forget('cart_id');

            // Step 9: Return success response
            $orderResource = new OrderResource($order);

            
            return response()->json([
                'message' => 'Order placed successfully',
                'order' => $orderResource, // Include serialized order data
            ], 200);

        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
