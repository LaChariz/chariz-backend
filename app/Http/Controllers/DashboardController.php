<?php

namespace App\Http\Controllers;

use App\Models\BillingDetail;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // public function getDashboardData()
    // {
    //     $totalProfits = $this->getTotalProfits();

    //     $newCustomers = $this->getCustomers('new');
    //     $activeCustomers = $this->getCustomers('active');

    //     $recentPurchases = $this->getRecentPurchases();

    //     return [
    //         'total_profits' => $totalProfits,
    //         'new_customers' => $newCustomers,
    //         'active_customers' => $activeCustomers,
    //         'recent_purchases' => $recentPurchases,
    //     ];
    // }
    
    public function getDashboardData()
    {
        $currentYearStart = now()->startOfYear();
        $currentMonthStart = now()->startOfMonth();

        // Calculate total profits
        $totalProfits = [
            'this_year' => $this->getTotalProfits(['start' => $currentYearStart, 'end' => now()]),
            'this_month' => $this->getTotalProfits(['start' => $currentMonthStart, 'end' => now()]),
            'all_time' => $this->getTotalProfits(),
        ];

        // Get new and active customer counts
        $newCustomers = [
            'this_year' => $this->getCustomers('new', ['start' => $currentYearStart, 'end' => now()]),
            'this_month' => $this->getCustomers('new', ['start' => $currentMonthStart, 'end' => now()]),
            'all_time' => $this->getCustomers('new'),
        ];

        $activeCustomers = [
            'this_year' => $this->getCustomers('active', ['start' => $currentYearStart, 'end' => now()]),
            'this_month' => $this->getCustomers('active', ['start' => $currentMonthStart, 'end' => now()]),
            'all_time' => $this->getCustomers('active'),
        ];

        $recentPurchases = $this->getRecentPurchases();

        return [
            'total_profits' => $totalProfits,
            'new_customers' => $newCustomers,
            'active_customers' => $activeCustomers,
            'recent_purchases' => $recentPurchases,
        ];
    }


    public function getRecentPurchases()
    {
        return OrderItem::with([
            'product:id,product_name',
            'order.billingDetails' => function ($query) {
                $query->select(['firstname', 'lastname', 'id']);
            },
            'order:id,status'
        ])
        ->latest()
        ->take(5)
        ->get();
    }

    public function getCustomers($type, $period = null)
    {
        if ($type === 'new') {
            return BillingDetail::distinct()
                ->when($period, function ($query) use ($period) {
                    return $query->whereBetween('created_at', [$period['start'], $period['end']]);
                })
                ->count('email');
        } else {
            return Order::distinct()
                ->when($period, function ($query) use ($period) {
                    return $query->whereBetween('created_at', [$period['start'], $period['end']]);
                })
                ->count('user_id');
        }
    }

    public function getTotalProfits($period = null)
    {
        $query = Order::whereHas('orderItems.product', function ($query) {
            $query->whereNotNull('cost_price');
        })
        ->where('status', 'delivered')
        ->with(['orderItems.product' => function ($query) {
            $query->select(['id', 'price', 'cost_price']);
        }])
        ->select(['id']);

        if ($period) {
            $query->whereBetween('created_at', [$period['start'], $period['end']]);
        }

        $orders = $query->get();

        return $orders->sum(function ($order) {
            return $order->orderItems->sum(function ($item) {
                return ($item->product->price - $item->product->cost_price) * $item->quantity;
            });
        });
    }

    // private function getTotalProfits()
    // {
    //     $currentMonthProfits = $this->getProfitsByPeriod('month');
    //     $currentYearProfits = $this->getProfitsByPeriod('year');
    //     $allTimeProfits = $this->getProfitsByPeriod('all');

    //     return [
    //         'current_month_profits' => $currentMonthProfits,
    //         'current_year_profits' => $currentYearProfits,
    //         'all_time_profits' => $allTimeProfits
    //     ];
    // }

    // private function getProfitsByPeriod($period)
    // {
    //     $query = DB::table('orders')
    //         ->join('order_items', 'orders.id', '=', 'order_items.order_id')
    //         ->join('products', 'order_items.product_id', '=', 'products.id')
    //         ->where('orders.status', 'delivered')
    //         ->whereNotNull('products.cost_price')
    //         ->select(DB::raw('SUM(products.price - products.cost_price) as total_profit'));

    //     switch ($period) {
    //         case 'month':
    //             $query->whereMonth('orders.created_at', now()->month);
    //             break;
    //         case 'year':
    //             $query->whereYear('orders.created_at', now()->year);
    //             break;
    //     }

    //     $profits = $query->first();

    //     return $profits ? $profits->total_profit : 0;
    // }

    // private function getCustomerStats()
    // {
    //     $currentMonthNewCustomers = $this->getNewCustomersByPeriod('month');
    //     $currentYearNewCustomers = $this->getNewCustomersByPeriod('year');
    //     $allTimeNewCustomers = $this->getNewCustomersByPeriod('all');

    //     $currentMonthActiveCustomers = $this->getActiveCustomersByPeriod('month');
    //     $currentYearActiveCustomers = $this->getActiveCustomersByPeriod('year');
    //     $allTimeActiveCustomers = $this->getActiveCustomersByPeriod('all');

    //     return [
    //         'current_month_new_customers' => $currentMonthNewCustomers,
    //         'current_year_new_customers' => $currentYearNewCustomers,
    //         'all_time_new_customers' => $allTimeNewCustomers,
    //         'current_month_active_customers' => $currentMonthActiveCustomers,
    //         'current_year_active_customers' => $currentYearActiveCustomers,
    //         'all_time_active_customers' => $allTimeActiveCustomers
    //     ];
    // }

    // private function getNewCustomersByPeriod($period)
    // {
    //     return BillingDetail::query()
    //         ->whereHas('order', function ($query) use ($period) {
    //             $this->applyPeriodFilter($query, $period);
    //         })
    //         ->where('id', '=', 'orders.id') 
    //         ->groupBy('email')
    //         ->count();
    // }

    // private function getActiveCustomersByPeriod($period)
    // {
    //     return BillingDetail::query()
    //         ->whereHas('order', function ($query) use ($period) {
    //             $this->applyPeriodFilter($query, $period);
    //         })
    //         ->distinct('email')
    //         ->count('email');
    // }

    // private function applyPeriodFilter($query, $period)
    // {
    //     switch ($period) {
    //         case 'month':
    //             $query->whereMonth('created_at', now()->month);
    //             break;
    //         case 'year':
    //             $query->whereYear('created_at', now()->year);
    //             break;
    //     }
    // }

    // private function getRecentPurchaseHistory()
    // {
    //     $recentPurchases = DB::table('order_items')
    //         ->join('orders', 'order_items.order_id', '=', 'orders.id')
    //         ->join('products', 'order_items.product_id', '=', 'products.id')
    //         ->join('billing_details', 'orders.billing_details_id', '=', 'billing_details.id')
    //         ->select(
    //             'products.id as product_id',
    //             'products.product_name',
    //             DB::raw('CONCAT(billing_details.firstname, " ", billing_details.lastname) as customer_name'),
    //             'order_items.quantity',
    //             'order_items.total_price',
    //             'orders.status'
    //         )
    //         ->orderBy('orders.created_at', 'desc')
    //         ->get();

    //     return $recentPurchases;
    // }
}