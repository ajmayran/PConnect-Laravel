<?php

namespace App\Http\Controllers\Distributors;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $distributor = Auth::user()->distributor;
        $distributorId = $distributor->id;
        
        // Get time periods for queries
        $today = Carbon::today();
        $last30Days = Carbon::today()->subDays(30);
        $last90Days = Carbon::today()->subDays(90);
        
        // Calculate total sales
        $totalSales = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('products.distributor_id', $distributorId)
            ->where('orders.status', 'completed')
            ->sum(DB::raw('order_details.quantity * order_details.price'));
            
        // Calculate total orders
        $totalOrders = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('products.distributor_id', $distributorId)
            ->distinct('orders.id')
            ->count('orders.id');
            
        // Order statuses count
        $orderStatuses = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('products.distributor_id', $distributorId)
            ->select('orders.status', DB::raw('COUNT(DISTINCT orders.id) as count'))
            ->groupBy('orders.status')
            ->pluck('count', 'status')
            ->toArray();
            
        // Get total products
        $totalProducts = DB::table('products')
            ->where('distributor_id', $distributorId)
            ->count();
            
        // Get total unique customers
        $totalCustomers = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('products.distributor_id', $distributorId)
            ->distinct('orders.user_id')
            ->count('orders.user_id');
            
        // Daily sales data for the past 30 days
        $dailySales = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('products.distributor_id', $distributorId)
            ->where('orders.created_at', '>=', $last30Days)
            ->where('orders.status', 'completed')
            ->select(
                DB::raw('Date(orders.created_at) as date'),
                DB::raw('SUM(order_details.quantity * order_details.price) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map(function ($item) {
                return round($item->total, 2);
            })
            ->toArray();
            
        // Fill in missing dates with zero values
        $salesData = [];
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $salesData[$date] = $dailySales[$date] ?? 0;
        }
            
        // Daily orders data for chart
        $dailyOrders = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('products.distributor_id', $distributorId)
            ->where('orders.created_at', '>=', $last30Days)
            ->select(
                DB::raw('Date(orders.created_at) as date'),
                DB::raw('COUNT(DISTINCT orders.id) as count'),
                'orders.status'
            )
            ->groupBy('date', 'orders.status')
            ->orderBy('date')
            ->get();
            
        // Format orders data for chart
        $orderData = [
            'completed' => [],
            'pending' => [],
            'cancelled' => [],
            'processing' => [],
            'delivering' => []
        ];
        
        foreach ($dailyOrders as $order) {
            $status = strtolower($order->status);
            if (!isset($orderData[$status][$order->date])) {
                $orderData[$status][$order->date] = 0;
            }
            $orderData[$status][$order->date] += $order->count;
        }
        
        // Dynamically get columns from products table to prevent SQL errors
        $productColumns = Schema::getColumnListing('products');
        
        // Build query for top selling products based on available columns
        $query = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('products.distributor_id', $distributorId)
            ->where('orders.status', 'completed')
            ->where('orders.created_at', '>=', $last30Days);
        
        // Select only columns that exist
        $selectColumns = ['products.id'];
        $groupByColumns = ['products.id'];
        
        // Add name column if it exists
        if (in_array('name', $productColumns)) {
            $selectColumns[] = 'products.name';
            $groupByColumns[] = 'products.name';
        } else if (in_array('product_name', $productColumns)) {
            // Try alternative column name
            $selectColumns[] = 'products.product_name as name';
            $groupByColumns[] = 'products.product_name';
        } else {
            // Fallback - use ID as name
            $selectColumns[] = 'products.id as name';
        }
        
        // Add image column if it exists
        if (in_array('image', $productColumns)) {
            $selectColumns[] = 'products.image';
            $groupByColumns[] = 'products.image';
        } else if (in_array('product_image', $productColumns)) {
            $selectColumns[] = 'products.product_image as image';
            $groupByColumns[] = 'products.product_image';
        } else {
            // No image column, add null
            $selectColumns[] = DB::raw('NULL as image');
        }
        
        // Add category column if it exists
        if (in_array('category', $productColumns)) {
            $selectColumns[] = 'products.category';
            $groupByColumns[] = 'products.category';
        } else if (in_array('product_category', $productColumns) || in_array('category_id', $productColumns)) {
            // Try alternative column names
            $categoryColumn = in_array('product_category', $productColumns) ? 'product_category' : 'category_id';
            $selectColumns[] = "products.{$categoryColumn} as category";
            $groupByColumns[] = "products.{$categoryColumn}";
        } else {
            // No category column, add placeholder
            $selectColumns[] = DB::raw("'Uncategorized' as category");
        }
        
        // Add sales metrics
        $selectColumns[] = DB::raw('SUM(order_details.quantity) as total_sold');
        $selectColumns[] = DB::raw('SUM(order_details.quantity * order_details.price) as total_revenue');
        
        // Apply the columns to the query and get results
        $topProducts = $query
            ->select($selectColumns)
            ->groupBy($groupByColumns)
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();
        
        // Customer engagement data (cart additions)
        try {
            $cartAdditions = DB::table('cart_items')
                ->join('products', 'cart_items.product_id', '=', 'products.id')
                ->where('products.distributor_id', $distributorId)
                ->where('cart_items.created_at', '>=', $last30Days)
                ->select(
                    DB::raw('Date(cart_items.created_at) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date')
                ->map(function ($item) {
                    return $item->count;
                })
                ->toArray();
        } catch (\Exception $e) {
            // Handle case where cart_items table doesn't exist or has different structure
            $cartAdditions = [];
            Log::warning('Error fetching cart additions: ' . $e->getMessage());
        }
            
        // Fill in missing dates for cart data
        $cartData = [];
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $cartData[$date] = $cartAdditions[$date] ?? 0;
        }

        // Prepare dashboard data for sending to the view
        $dashboardData = [
            'totalSales' => $totalSales ?? 0,
            'totalOrders' => $totalOrders ?? 0,
            'totalProducts' => $totalProducts ?? 0,
            'totalCustomers' => $totalCustomers ?? 0,
            'orderStatuses' => $orderStatuses ?? [],
            'salesData' => $salesData ?? [],
            'orderData' => $orderData ?? [],
            'topProducts' => $topProducts ?? [],
            'cartData' => $cartData ?? []
        ];

        return view('distributors.dashboard', [
            'dashboardData' => $dashboardData
        ]);
    }
    
    /**
     * Get sales data for a specific period
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSalesData(Request $request)
    {
        $period = $request->input('period', 30); // Default to 30 days
        
        // Get distributor ID from authenticated user
        $distributorId = Auth::user()->distributor->id;
        
        // Query for sales data based on selected period
        $startDate = Carbon::today()->subDays($period);
        $endDate = Carbon::today();
        
        // Get sales data grouped by date
        $salesData = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('products.distributor_id', $distributorId)
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(orders.created_at) as date'),
                DB::raw('SUM(order_details.quantity * order_details.price) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map(function ($item) {
                return round($item->total, 2);
            })
            ->toArray();
        
        // Fill in missing dates with zero values
        $result = [];
        $current = clone $startDate;
        
        while ($current <= $endDate) {
            $dateString = $current->format('Y-m-d');
            $result[$dateString] = $salesData[$dateString] ?? 0;
            $current->addDay();
        }
        
        return response()->json($result);
    }
}