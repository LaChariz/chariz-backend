<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function getCustomers()
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $customers = DB::table('billing_details')
            ->select(
                'billing_details.email',
                DB::raw('MAX(orders.created_at) as last_active'), 
                DB::raw('COUNT(orders.id) as total_orders'), 
                DB::raw('SUM(orders.total_price) as total_orders_amount')
            )
            ->leftJoin('orders', 'billing_details.id', '=', 'orders.billing_details_id')
            ->groupBy('billing_details.email')
            ->get();

        foreach ($customers as $customer) {
            $billingDetails = DB::table('billing_details')
                ->where('email', $customer->email)
                ->first();

            $customer->name = $billingDetails->firstname . ' ' . $billingDetails->lastname;
        }

        return $customers;
    }
}
