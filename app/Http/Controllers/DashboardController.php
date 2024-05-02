<?php

namespace App\Http\Controllers;

use App\Models\BillingDetail;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getDashboardData()
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $totalProfits = $this->getTotalProfits();
        $customerStats = $this->getCustomerStats();
        // $recentPurchaseHistory = $this->getRecentPurchaseHistory();

        return response()->json([
            'total_profits' => $totalProfits,
            'customer_stats' => $customerStats,
            // 'recent_purchase_history' => $recentPurchaseHistory,
        ]);
    }

    private function getTotalProfits()
    {
        $currentMonthProfits = $this->getProfitsByPeriod('month');
        $currentYearProfits = $this->getProfitsByPeriod('year');
        $allTimeProfits = $this->getProfitsByPeriod('all');

        return [
            'current_month_profits' => $currentMonthProfits,
            'current_year_profits' => $currentYearProfits,
            'all_time_profits' => $allTimeProfits
        ];
    }

    private function getProfitsByPeriod($period)
    {
        $query = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', 'delivered')
            ->whereNotNull('products.cost_price')
            ->select(DB::raw('SUM(products.price - products.cost_price) as total_profit'));

        switch ($period) {
            case 'month':
                $query->whereMonth('orders.created_at', now()->month);
                break;
            case 'year':
                $query->whereYear('orders.created_at', now()->year);
                break;
        }

        $profits = $query->first();

        return $profits ? $profits->total_profit : 0;
    }

    private function getCustomerStats()
    {
        $currentMonthNewCustomers = $this->getNewCustomersByPeriod('month');
        $currentYearNewCustomers = $this->getNewCustomersByPeriod('year');
        $allTimeNewCustomers = $this->getNewCustomersByPeriod('all');

        $currentMonthActiveCustomers = $this->getActiveCustomersByPeriod('month');
        $currentYearActiveCustomers = $this->getActiveCustomersByPeriod('year');
        $allTimeActiveCustomers = $this->getActiveCustomersByPeriod('all');

        return [
            'current_month_new_customers' => $currentMonthNewCustomers,
            'current_year_new_customers' => $currentYearNewCustomers,
            'all_time_new_customers' => $allTimeNewCustomers,
            'current_month_active_customers' => $currentMonthActiveCustomers,
            'current_year_active_customers' => $currentYearActiveCustomers,
            'all_time_active_customers' => $allTimeActiveCustomers
        ];
    }

    private function getNewCustomersByPeriod($period)
    {
        return BillingDetail::query()
            ->whereHas('order', function ($query) use ($period) {
                $this->applyPeriodFilter($query, $period);
            })
            ->where('id', '=', 'orders.id') 
            ->groupBy('email')
            ->count();
    }

    private function getActiveCustomersByPeriod($period)
    {
        return BillingDetail::query()
            ->whereHas('order', function ($query) use ($period) {
                $this->applyPeriodFilter($query, $period);
            })
            ->distinct('email')
            ->count('email');
    }

    private function applyPeriodFilter($query, $period)
    {
        switch ($period) {
            case 'month':
                $query->whereMonth('created_at', now()->month);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }
    }

    private function getRecentPurchaseHistory()
    {
        $recentPurchases = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('billing_details', 'orders.billing_details_id', '=', 'billing_details.id')
            ->select(
                'products.id as product_id',
                'products.product_name',
                DB::raw('CONCAT(billing_details.firstname, " ", billing_details.lastname) as customer_name'),
                'order_items.quantity',
                'order_items.total_price',
                'orders.status'
            )
            ->orderBy('orders.created_at', 'desc')
            ->get();

        return $recentPurchases;
    }
}