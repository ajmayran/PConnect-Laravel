<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Earning;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InsightsController extends Controller
{
    public function index()
    {
        $distributor_id = Auth::user()->distributor->id;
        $period = request('period', '365'); // Default to 1 year

        // Get date range based on period
        $startDate = $this->getStartDate($period);

        // Get earnings data with relationships
        $earnings = Earning::where('distributor_id', $distributor_id)
            ->when($period != 'all', function ($query) use ($startDate) {
                return $query->where('created_at', '>=', $startDate);
            })
            ->with(['payment.order.user', 'payment.order.orderDetails'])
            ->latest();

        // Calculate total earnings from the earnings table
        $totalEarnings = $earnings->sum('amount');

        // Get monthly earnings for the chart
        $monthlyEarnings = Earning::where('distributor_id', $distributor_id)
            ->when($period != 'all', function ($query) use ($startDate) {
                return $query->where('created_at', '>=', $startDate);
            })
            ->selectRaw('SUM(amount) as total, MONTH(created_at) as month, YEAR(created_at) as year')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Calculate growth rate
        $growthRate = $this->calculateGrowthRate($distributor_id, $period);

        // Get recent earnings
        $recentEarnings = $earnings->take(5)->get();

        // Get top selling products
        $topProducts = $this->getTopSellingProducts($distributor_id, $startDate);

        // Get weekly sales trends
        $weeklySalesTrend = $this->getWeeklySalesTrend($distributor_id, $startDate);

        return view('distributors.insights.index', compact(
            'totalEarnings',
            'monthlyEarnings',
            'recentEarnings',
            'growthRate',
            'topProducts',
            'weeklySalesTrend',
            'period'
        ));
    }

    /**
     * Handle AJAX request for fetching insights data
     */
    public function getInsightsData(Request $request)
    {
        $distributor_id = Auth::user()->distributor->id;
        $period = $request->get('period', '365');

        // Get date range based on period
        $startDate = $this->getStartDate($period);

        // Get monthly earnings data
        $monthlyEarnings = Earning::where('distributor_id', $distributor_id)
            ->when($period != 'all', function ($query) use ($startDate) {
                return $query->where('created_at', '>=', $startDate);
            })
            ->selectRaw('SUM(amount) as total, MONTH(created_at) as month, YEAR(created_at) as year')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Calculate totals and growth
        $totalEarnings = Earning::where('distributor_id', $distributor_id)
            ->when($period != 'all', function ($query) use ($startDate) {
                return $query->where('created_at', '>=', $startDate);
            })
            ->sum('amount');

        // Get top selling products
        $topProducts = $this->getTopSellingProducts($distributor_id, $startDate);

        // Get weekly sales trends
        $weeklySalesTrend = $this->getWeeklySalesTrend($distributor_id, $startDate);

        // Calculate this month's earnings
        $currentMonthEarnings = Earning::where('distributor_id', $distributor_id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // Calculate average monthly earnings
        $avgMonthlyEarnings = $monthlyEarnings->avg('total') ?? 0;

        // Calculate growth rate
        $growthRate = $this->calculateGrowthRate($distributor_id, $period);

        return response()->json([
            'totalEarnings' => $totalEarnings,
            'monthlyEarnings' => $monthlyEarnings,
            'currentMonthEarnings' => $currentMonthEarnings,
            'averageMonthlyEarnings' => $avgMonthlyEarnings,
            'growthRate' => $growthRate,
            'topProducts' => $topProducts,
            'weeklySalesTrend' => $weeklySalesTrend
        ]);
    }

    /**
     * Helper method to get start date based on period
     */
    private function getStartDate($period)
    {
        if ($period == 'all') {
            return null;
        }

        return match ($period) {
            '30' => Carbon::now()->subDays(30),
            '90' => Carbon::now()->subDays(90),
            '365' => Carbon::now()->subYear(),
            default => Carbon::now()->subDays(intval($period))
        };
    }

    /**
     * Calculate growth rate compared to previous period
     */
    private function calculateGrowthRate($distributor_id, $period)
    {
        // Get current period start date
        $currentPeriodStart = $this->getStartDate($period);

        if ($currentPeriodStart === null) {
            // For "all time" we can't calculate growth rate
            return '0%';
        }

        // Get previous period (same length, immediately before current period)
        $previousPeriodStart = match ($period) {
            '30' => Carbon::now()->subDays(60)->startOfDay(),
            '90' => Carbon::now()->subDays(180)->startOfDay(),
            '365' => Carbon::now()->subYears(2)->startOfDay(),
            default => Carbon::now()->subDays(intval($period) * 2)->startOfDay()
        };

        $previousPeriodEnd = $currentPeriodStart->copy()->subDay()->endOfDay();

        // Calculate earnings for current period
        $currentPeriodEarnings = Earning::where('distributor_id', $distributor_id)
            ->where('created_at', '>=', $currentPeriodStart)
            ->sum('amount');

        // Calculate earnings for previous period
        $previousPeriodEarnings = Earning::where('distributor_id', $distributor_id)
            ->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
            ->sum('amount');

        // Calculate growth rate
        if ($previousPeriodEarnings == 0) {
            return $currentPeriodEarnings > 0 ? '100%' : '0%';
        }

        $growthRate = (($currentPeriodEarnings - $previousPeriodEarnings) / $previousPeriodEarnings) * 100;

        return round($growthRate) . '%';
    }

    /**
     * Get top selling products
     */
    private function getTopSellingProducts($distributor_id, $startDate)
    {
        return DB::table('products')
            ->join('order_details', 'products.id', '=', 'order_details.product_id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('products.distributor_id', $distributor_id)
            ->where('orders.status', 'completed')
            ->when($startDate, function ($query) use ($startDate) {
                return $query->where('orders.created_at', '>=', $startDate);
            })
            ->select(
                'products.id',
                'products.product_name as name', // Changed from products.name to products.product_name with alias
                DB::raw('SUM(order_details.quantity) as units_sold'),
                DB::raw('SUM(order_details.quantity * order_details.price) as revenue')
            )
            ->groupBy('products.id', 'products.product_name') // Changed from products.name to products.product_name
            ->orderByDesc('units_sold')
            ->limit(5)
            ->get();
    }

    /**
     * Get weekly sales trends
     */
    private function getWeeklySalesTrend($distributor_id, $startDate)
    {
        // For weekly trends, we'll get data for the last 6 weeks
        $weeks = collect([]);

        // Create week ranges
        for ($i = 5; $i >= 0; $i--) {
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
            $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();

            $weeks->push([
                'week_number' => Carbon::now()->subWeeks($i)->weekOfYear,
                'start_date' => $weekStart,
                'end_date' => $weekEnd,
                'label' => 'Week ' . Carbon::now()->subWeeks($i)->weekOfYear,
            ]);
        }

        // Get sales data for each week
        $weeklyData = $weeks->map(function ($week) use ($distributor_id) {
            $earnings = Earning::where('distributor_id', $distributor_id)
                ->whereBetween('created_at', [$week['start_date'], $week['end_date']])
                ->sum('amount');

            return [
                'week' => $week['label'],
                'earnings' => $earnings,
            ];
        });

        return $weeklyData;
    }

    public function allEarnings()
    {
        $distributor_id = Auth::user()->distributor->id;

        // Fetch all earnings for the distributor
        $earnings = Earning::where('distributor_id', $distributor_id)
            ->with(['payment.order.user', 'payment.order.orderDetails'])
            ->latest()
            ->paginate(20); // Paginate results

        return view('distributors.insights.all-earnings', compact('earnings'));
    }
}
