<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Order;
use App\Models\Distributors;
use App\Models\RetailerReport;
use App\Models\DistributorReport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{

    public function index()
    {
        // Fetch the number of active retailers
        $activeRetailersCount = User::where('user_type', 'retailer')->where('status', 'active')->count();

        // Fetch the number of active orders
        $activeOrdersCount = DB::table('orders')->where('status', 'active')->count();

        // Fetch the number of completed orders
        $completedOrdersCount = DB::table('orders')->where('status', 'completed')->count();

        // Fetch the number of canceled orders
        $canceledOrdersCount = DB::table('orders')->where('status', 'canceled')->count();

        // Fetch the total number of users (retailers and approved distributors only)
        $totalUsersCount = User::where(function ($query) {
            $query->where('user_type', 'retailer')
                  ->orWhere(function ($query) {
                      $query->where('user_type', 'distributor')
                            ->where('status', 'approved');
                  });
        })->count();
        
        // Get recent tickets
        $recentTickets = Ticket::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get reports grouped by reason
        $retailerReports = RetailerReport::select('reason', DB::raw('count(*) as count'))
            ->groupBy('reason')
            ->orderBy('count', 'desc')
            ->get();
            
        $distributorReports = DistributorReport::select('reason', DB::raw('count(*) as count'))
            ->groupBy('reason')
            ->orderBy('count', 'desc')
            ->get();

        // Get order data for charts - last 30 days
        $startDate = Carbon::now()->subDays(30);
        
        // Order trend data for chart
        $orderTrends = DB::table('orders')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(CASE WHEN status = "active" THEN 1 END) as active_count'),
                DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_count'),
                DB::raw('COUNT(CASE WHEN status = "canceled" THEN 1 END) as canceled_count')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Prepare data for charts
        $dates = [];
        $activeOrdersData = [];
        $completedOrdersData = [];
        $canceledOrdersData = [];
        
        // Generate all dates in the range
        $period = Carbon::parse($startDate)->daysUntil(Carbon::now());
        
        // Initialize with zeroes
        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $dates[] = $date->format('M d'); // Format for display (e.g., "Jan 01")
            $activeOrdersData[$dateString] = 0;
            $completedOrdersData[$dateString] = 0;
            $canceledOrdersData[$dateString] = 0;
        }
        
        // Fill in actual data
        foreach ($orderTrends as $trend) {
            $dateString = $trend->date;
            if (isset($activeOrdersData[$dateString])) {
                $activeOrdersData[$dateString] = $trend->active_count;
                $completedOrdersData[$dateString] = $trend->completed_count;
                $canceledOrdersData[$dateString] = $trend->canceled_count;
            }
        }
        
        // Convert associative arrays to simple arrays for the chart
        $activeOrdersChartData = array_values($activeOrdersData);
        $completedOrdersChartData = array_values($completedOrdersData);
        $canceledOrdersChartData = array_values($canceledOrdersData);
        
        // Format retailer and distributor report data for the chart
        $retailerReportLabels = $retailerReports->pluck('reason')->toArray();
        $retailerReportData = $retailerReports->pluck('count')->toArray();
        
        $distributorReportLabels = $distributorReports->pluck('reason')->toArray();
        $distributorReportData = $distributorReports->pluck('count')->toArray();
        
        // If either report type has fewer than 5 categories, pad with empty values
        while (count($retailerReportLabels) < 5) {
            $retailerReportLabels[] = '';
            $retailerReportData[] = 0;
        }
        
        while (count($distributorReportLabels) < 5) {
            $distributorReportLabels[] = '';
            $distributorReportData[] = 0;
        }

        // Pass the data to the view
        return view('admin.dashboard', [
            'user' => Auth::user(),
            'activeRetailersCount' => $activeRetailersCount,
            'activeOrdersCount' => $activeOrdersCount,
            'completedOrdersCount' => $completedOrdersCount,
            'canceledOrdersCount' => $canceledOrdersCount,
            'totalUsersCount' => $totalUsersCount,
            'recentTickets' => $recentTickets,
            'retailerReports' => $retailerReports,
            'distributorReports' => $distributorReports,
            'chartDates' => $dates,
            'activeOrdersChartData' => $activeOrdersChartData,
            'completedOrdersChartData' => $completedOrdersChartData,
            'canceledOrdersChartData' => $canceledOrdersChartData,
            'retailerReportLabels' => $retailerReportLabels,
            'retailerReportData' => $retailerReportData,
            'distributorReportLabels' => $distributorReportLabels,
            'distributorReportData' => $distributorReportData
        ]);
    }
    
    public function getChartData(Request $request)
    {
        $days = $request->input('days', 30);
        $startDate = Carbon::now()->subDays($days);
        
        // Order trend data for chart
        $orderTrends = DB::table('orders')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(CASE WHEN status = "active" THEN 1 END) as active_count'),
                DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_count'),
                DB::raw('COUNT(CASE WHEN status = "canceled" THEN 1 END) as canceled_count')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Prepare data for charts
        $dates = [];
        $activeOrdersData = [];
        $completedOrdersData = [];
        $canceledOrdersData = [];
        
        // Generate all dates in the range
        $period = Carbon::parse($startDate)->daysUntil(Carbon::now());
        
        // Initialize with zeroes
        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $dates[] = $date->format('M d'); // Format for display (e.g., "Jan 01")
            $activeOrdersData[$dateString] = 0;
            $completedOrdersData[$dateString] = 0;
            $canceledOrdersData[$dateString] = 0;
        }
        
        // Fill in actual data
        foreach ($orderTrends as $trend) {
            $dateString = $trend->date;
            if (isset($activeOrdersData[$dateString])) {
                $activeOrdersData[$dateString] = $trend->active_count;
                $completedOrdersData[$dateString] = $trend->completed_count;
                $canceledOrdersData[$dateString] = $trend->canceled_count;
            }
        }
        
        return response()->json([
            'dates' => $dates,
            'activeOrders' => array_values($activeOrdersData),
            'completedOrders' => array_values($completedOrdersData),
            'canceledOrders' => array_values($canceledOrdersData)
        ]);
    }
}