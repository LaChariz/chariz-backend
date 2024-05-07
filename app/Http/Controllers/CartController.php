<?php

// namespace App\Http\Controllers;

// use App\Models\Cart;
// use App\Models\CartItem;
// use App\Http\Resources\CartItemResource;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Session;
// use Illuminate\Support\Facades\Log;

// class CartController extends Controller
// {
//     public function addToCart(Request $request)
//     {
//         try {
//             $request->validate([
//                 'product_id' => 'required|exists:products,id',
//                 'quantity' => 'required|integer|min:1',
//             ]);
    
//             if (Auth::check()) {
//                 $user = Auth::user();
//                 $cart = $user->cart()->firstOrCreate();
//                 $cartItem = $cart->cartItems()->updateOrCreate(
//                     ['product_id' => $request->product_id],
//                     ['quantity' => $request->quantity],
//                     ['user_id' => $user->id]
//                 );
//             } else {
//                 $cartId = Session::get('cart_id');
//                 $cart = null;
    
//                 if (!$cartId) {
//                     $cart = Cart::create(['user_id' => null]);
//                     Session::put('cart_id', $cart->id);
//                 } else {
//                     $cart = Cart::find($cartId);
//                 }
    
//                 // If cart is still not found, create a new one
//                 if (!$cart) {
//                     $cart = Cart::create(['user_id' => null]);
//                     Session::put('cart_id', $cart->id);
//                 }
    
//                 // Now the $cart object should not be null
//                 $existingCartItem = $cart->cartItems()->where('product_id', $request->product_id)->first();
//                 if ($existingCartItem) {
//                     $existingCartItem->update(['quantity' => $existingCartItem->quantity + $request->quantity]);
//                 } else {
//                     $cartItem = new CartItem([
//                         'product_id' => $request->product_id,
//                         'quantity' => $request->quantity,
//                     ]);
//                     $cart->cartItems()->save($cartItem);
//                 }
//             }
    
//             return response()->json(['message' => 'Product added to cart successfully']);
    
//         } catch(\Exception $e) {
//             return response()->json([
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     public function removeFromCart(Request $request, $cartItemId)
//     {
//         if (Auth::check()) {
//             $user = Auth::user();
//             $cartItem = $user->cart->cartItems()->findOrFail($cartItemId);
//         } else {
//             $cartId = Session::get('cart_id');
//             $cartItem = CartItem::where('cart_id', $cartId)->findOrFail($cartItemId);
//         }

//         $cartItem->delete();

//         return response()->json(['message' => 'Product removed from cart successfully']);
//     }

//     public function updateCart(Request $request)
//     {
//         try {
//             $request->validate([
//                 'cart_item_id' => 'required|integer|min:1',
//                 'quantity' => 'required|integer|min:1',
//             ]);

//             $cartItemId = $request->cart_item_id;

//             if (Auth::check()) {
//                 $user = Auth::user();
//                 $cart = $user->cart()->firstOrCreate();
//                 $cartItem = $cart->cartItems()->findOrFail($cartItemId);
//             } else {
//                 $cartId = Session::get('cart_id');
//                 if (!$cartId) {
//                     return response()->json(['error' => 'Cart not found in session'], 404);
//                 }
//                 $cart = Cart::findOrFail($cartId);
//                 $cartItem = $cart->cartItems()->findOrFail($cartItemId);
//             }

//             $cartItem->update(['quantity' => $request->quantity]);

//             return response()->json(['message' => 'Cart item quantity updated successfully']);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     public function viewCart(Request $request)
//     {
//         try {
//             if (Auth::check()) {
//                 $user = Auth::user();
//                 $cart = $user->cart;
//                 if (!$cart) {
//                     $cartItems = [];
//                     return response()->json(['cart_items' => $cartItems]);
//                 }
//             } else {
//                 $cartId = Session::get('cart_id');
//                 if (!$cartId) {
//                     $cartItems = [];
//                     return response()->json(['cart_items' => $cartItems]);
//                 }
//                 $cart = Cart::find($cartId);
//                 if (!$cart) {
//                     $cartItems = [];
//                     return response()->json([
//                         'cart_items' => $cartItems,
//                         'total_cost' => 0,
//                     ]);
//                 }
//             }

//             $cartItems = $cart->cartItems()->with('product')->get();
//             $totalCost = 0;
//             foreach ($cartItems as $cartItem) {
//                 $totalCost += $cartItem->product->price * $cartItem->quantity;
//             }

//             return response()->json([
//                 'cart_items' => CartItemResource::collection($cartItems),
//                 'total_cost' => $totalCost,
//             ]);
            
//         } catch (\Exception $e) {
//             Log::error('Error retrieving cart: ' . $e->getMessage());
//             return response()->json(['error' => 'Error retrieving cart.'], 500);
//         }
//     }   

// }    
