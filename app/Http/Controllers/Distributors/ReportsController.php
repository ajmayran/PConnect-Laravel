<?php

namespace App\Http\Controllers\Distributors;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Delivery;
use App\Models\Earning;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class ReportsController extends Controller
{

    private function getProductReports($distributorId, $startDate)
    {
        // Get all products for this distributor
        $products = Product::where('distributor_id', $distributorId)
            ->with(['stocks', 'orderDetails.order' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->withTrashed()
            ->get();

        // Get products created within the period
        $createdProducts = Product::where('distributor_id', $distributorId)
            ->where('created_at', '>=', $startDate)
            ->pluck('id')
            ->toArray();

        // Get products deleted within the period
        $deletedProducts = Product::where('distributor_id', $distributorId)
            ->withTrashed()
            ->whereNotNull('deleted_at')
            ->where('deleted_at', '>=', $startDate)
            ->pluck('id')
            ->toArray();

        // Enhance with added, removed and sold data
        return $products->map(function ($product) use ($startDate, $createdProducts, $deletedProducts) {
            // Count additions to stock
            $added = $product->stocks
                ->where('type', 'in')
                ->where('created_at', '>=', $startDate)
                ->sum('quantity');

            // Count removals from stock
            $removed = $product->stocks
                ->where('type', 'out')
                ->where('created_at', '>=', $startDate)
                ->sum('quantity');

            // Count sales
            $sold = $product->orderDetails
                ->whereNotNull('order')
                ->filter(function ($detail) {
                    return $detail->order->status === 'completed';
                })
                ->sum('quantity');

            // Determine action type
            $action_type = 'updated';
            if (in_array($product->id, $createdProducts)) {
                $action_type = 'created';
            } elseif (in_array($product->id, $deletedProducts)) {
                $action_type = 'deleted';
            }

            return [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'price' => $product->price,
                'current_stock' => $product->stock_quantity,
                'added' => $added,
                'removed' => $removed,
                'sold' => $sold,
                'created_at' => $product->created_at->format('M d, Y'),
                'deleted_at' => $product->deleted_at,
                'action_type' => $action_type,
            ];
        });
    }


    private function getProductActivityByDate($distributorId, $startDate, $period)
    {
        // Define the date format based on the period
        $format = $period === 'yearly' ? '%Y-%m' : '%Y-%m-%d';

        // Get all products created within the period - NOTE THE '%' SYMBOLS ADDED
        $createdProducts = DB::table('products')
            ->where('distributor_id', $distributorId)
            ->where('created_at', '>=', $startDate)
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as date, COUNT(*) as count")
            ->groupBy('date')
            ->pluck('count', 'date');

        // Get all products deleted within the period - NOTE THE '%' SYMBOLS ADDED
        $deletedProducts = DB::table('products')
            ->where('distributor_id', $distributorId)
            ->whereNotNull('deleted_at')
            ->where('deleted_at', '>=', $startDate)
            ->selectRaw("DATE_FORMAT(deleted_at, '{$format}') as date, COUNT(*) as count")
            ->groupBy('date')
            ->pluck('count', 'date');

        // Get all products sold within the period - NOTE THE '%' SYMBOLS ADDED
        $soldProducts = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('products.distributor_id', $distributorId)
            ->where('orders.status', 'completed')
            ->where('orders.created_at', '>=', $startDate)
            ->selectRaw("DATE_FORMAT(orders.created_at, '{$format}') as date, SUM(order_details.quantity) as count")
            ->groupBy('date')
            ->pluck('count', 'date');

        // Generate the date range for the period
        $dates = [];
        $dateLabels = [];
        $current = Carbon::now();
        $start = Carbon::parse($startDate);

        if ($period === 'weekly') {
            for ($i = 6; $i >= 0; $i--) {
                $date = $current->copy()->subDays($i);
                $dates[] = $date->format('Y-m-d');
                $dateLabels[] = $date->format('D, M j');
            }
        } elseif ($period === 'monthly') {
            for ($i = 29; $i >= 0; $i--) {
                $date = $current->copy()->subDays($i);
                $dates[] = $date->format('Y-m-d');
                $dateLabels[] = $date->format('D, M j');
            }
        } else { // yearly
            for ($i = 11; $i >= 0; $i--) {
                $date = $current->copy()->subMonths($i);
                $dates[] = $date->format('Y-m');
                $dateLabels[] = $date->format('M Y');
            }
        }

        // Combine the data into the required format
        $activityByDate = [];
        foreach ($dates as $index => $date) {
            $activityByDate[] = [
                'date' => $dateLabels[$index],
                'added' => isset($createdProducts[$date]) ? (int)$createdProducts[$date] : 0,
                'removed' => isset($deletedProducts[$date]) ? (int)$deletedProducts[$date] : 0,
                'sold' => isset($soldProducts[$date]) ? (int)$soldProducts[$date] : 0,
            ];
        }

        return collect($activityByDate);
    }


    public function products(Request $request)
    {
        $distributor_id = Auth::user()->distributor->id;
        $period = $request->input('period', 'weekly');

        // Determine date range based on selected period
        $startDate = $this->getStartDate($period);

        // Get product data
        $products = $this->getProductReports($distributor_id, $startDate);

        // Group products by date for the timeline view
        $activityByDate = $this->getProductActivityByDate($distributor_id, $startDate, $period);

        // Prepare chart data for visualization
        $chartData = [
            'dates' => $activityByDate->pluck('date')->values()->toArray(),
            'added' => $activityByDate->pluck('added')->values()->toArray(),
            'removed' => $activityByDate->pluck('removed')->values()->toArray(),
            'sold' => $activityByDate->pluck('sold')->values()->toArray(),
        ];

        // Handle PDF export if requested
        if ($request->has('export') && $request->input('export') === 'pdf') {
            return $this->generatePdf('distributors.reports.pdf.products', [
                'products' => $products,
                'activityByDate' => $activityByDate,
                'period' => $period,
                'startDate' => $startDate,
            ], 'Products_Report_' . date('Y-m-d'));
        }

        return view('distributors.reports.products', [
            'products' => $products,
            'chartData' => $chartData,
            'activityByDate' => $activityByDate,
            'period' => $period,
        ]);
    }

    public function orders(Request $request)
    {
        $distributor_id = Auth::user()->distributor->id;
        $period = $request->input('period', 'weekly');

        // Determine date range based on selected period
        $startDate = $this->getStartDate($period);

        // Get orders data
        $orders = $this->getOrderReport($distributor_id, $startDate);

        // Calculate returned count for PDF template
        $returnedCount = $orders->filter(function ($order) {
            return $order->returnRequests && $order->returnRequests->where('status', 'approved')->count() > 0 ||
                ($order->payment && $order->payment->payment_status === 'refunded');
        })->count();

        // Count refunded orders using collection methods instead of whereHas
        $refundedCount = $orders->filter(function ($order) {
            return $order->payment && $order->payment->payment_status === 'refunded';
        })->count();

        // Prepare chart data
        $chartData = [
            'labels' => ['Completed', 'Returned', 'Refunded', 'Cancelled'],
            'values' => [
                $orders->where('status', 'completed')->count(),
                $returnedCount,
                $refundedCount,
                $orders->where('status', 'cancelled')->count(),
            ]
        ];

        // Handle PDF export if requested
        if ($request->has('export') && $request->input('export') === 'pdf') {
            return $this->generatePdf('distributors.reports.pdf.orders', [
                'orders' => $orders,
                'period' => $period,
                'startDate' => $startDate,
                'returnedCount' => $returnedCount,
            ], 'Orders_Report_' . date('Y-m-d'));
        }

        return view('distributors.reports.orders', [
            'orders' => $orders,
            'chartData' => $chartData,
            'period' => $period,
        ]);
    }

    /**
     * Display delivery reports.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delivery(Request $request)
    {
        $distributor_id = Auth::user()->distributor->id;
        $period = $request->input('period', 'weekly');

        // Determine date range based on selected period
        $startDate = $this->getStartDate($period);

        // Get deliveries data grouped by trucks
        $trucks = $this->getDeliveryReport($distributor_id, $startDate);

        // For chart data - prepare truck names and delivery counts
        $truckNames = [];
        $outForDeliveryCounts = [];
        $deliveredCounts = [];

        foreach ($trucks as $truck) {
            $truckNames[] = $truck->plate_number;
            $outForDeliveryCounts[] = $truck->deliveries->where('status', 'out_for_delivery')->count();
            $deliveredCounts[] = $truck->deliveries->where('status', 'delivered')->count();
        }

        $chartData = [
            'labels' => $truckNames,
            'outForDelivery' => $outForDeliveryCounts,
            'delivered' => $deliveredCounts,
        ];

        // Handle PDF export if requested
        if ($request->has('export') && $request->input('export') === 'pdf') {
            return $this->generatePdf('distributors.reports.pdf.delivery', [
                'trucks' => $trucks,
                'period' => $period,
                'startDate' => $startDate,
            ], 'Delivery_Report_' . date('Y-m-d'));
        }

        return view('distributors.reports.delivery', [
            'trucks' => $trucks,
            'chartData' => $chartData,
            'period' => $period,
        ]);
    }

    /**
     * Display revenue reports.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function revenue(Request $request)
    {
        $distributor_id = Auth::user()->distributor->id;
        $period = $request->input('period', 'weekly');

        // Determine date range based on selected period
        $startDate = $this->getStartDate($period);

        // Get revenue data
        $revenueData = $this->getRevenueReport($distributor_id, $startDate);

        // Format data for different time intervals based on period
        $chartLabels = [];
        $chartValues = [];

        if ($period === 'weekly') {
            // Group by day of week
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $chartLabels[] = $date->format('D');

                $dateStr = $date->format('Y-m-d');
                $chartValues[] = $revenueData->where('date', $dateStr)->first()?->total_revenue ?? 0;
            }
        } elseif ($period === 'monthly') {
            // Group by day of month
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $chartLabels[] = $date->format('j M');

                $dateStr = $date->format('Y-m-d');
                $chartValues[] = $revenueData->where('date', $dateStr)->first()?->total_revenue ?? 0;
            }
        } else {
            // Group by month
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $chartLabels[] = $date->format('M Y');

                $monthYear = $date->format('Y-m');
                $monthTotal = $revenueData
                    ->filter(function ($item) use ($monthYear) {
                        return strpos($item->date, $monthYear) === 0;
                    })
                    ->sum('total_revenue');

                $chartValues[] = $monthTotal;
            }
        }

        // Get top selling products for the period
        $topProducts = $this->getTopSellingProducts($distributor_id, $startDate);

        // Handle PDF export if requested
        if ($request->has('export') && $request->input('export') === 'pdf') {
            return $this->generatePdf('distributors.reports.pdf.revenue', [
                'revenueData' => $revenueData,
                'topProducts' => $topProducts,
                'period' => $period,
                'startDate' => $startDate,
                'totalRevenue' => array_sum($chartValues),
            ], 'Revenue_Report_' . date('Y-m-d'));
        }

        return view('distributors.reports.revenue', [
            'chartData' => [
                'labels' => $chartLabels,
                'values' => $chartValues,
            ],
            'topProducts' => $topProducts,
            'revenueData' => $revenueData,
            'period' => $period,
            'totalRevenue' => array_sum($chartValues),
        ]);
    }

    /**
     * Get start date based on period selection.
     *
     * @param  string  $period
     * @return \Carbon\Carbon
     */
    private function getStartDate($period)
    {
        switch ($period) {
            case 'weekly':
                return Carbon::now()->subDays(7);
            case 'monthly':
                return Carbon::now()->subDays(30);
            case 'yearly':
                return Carbon::now()->subYear();
            default:
                return Carbon::now()->subDays(7);
        }
    }

    /**
     * Generate product report data.
     *
     * @param  int  $distributorId
     * @param  \Carbon\Carbon  $startDate
     * @return \Illuminate\Support\Collection
     */
    private function getProductReport($distributorId, $startDate)
    {
        // Get all products for this distributor
        $products = Product::where('distributor_id', $distributorId)
            ->with(['stocks', 'orderDetails.order' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->withTrashed()
            ->get();

        // Enhance with added, removed and sold data
        return $products->map(function ($product) use ($startDate) {
            // Count additions to stock
            $added = $product->stocks
                ->where('type', 'in')
                ->where('created_at', '>=', $startDate)
                ->sum('quantity');

            // Count removals from stock
            $removed = $product->stocks
                ->where('type', 'out')
                ->where('created_at', '>=', $startDate)
                ->sum('quantity');

            // Count sales
            $sold = $product->orderDetails
                ->whereNotNull('order')
                ->filter(function ($detail) {
                    return $detail->order->status === 'completed';
                })
                ->sum('quantity');

            return [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'price' => $product->price,
                'current_stock' => $product->stock_quantity,
                'added' => $added,
                'removed' => $removed,
                'sold' => $sold,
                'deleted_at' => $product->deleted_at,
            ];
        });
    }

    /**
     * Generate order report data.
     *
     * @param  int  $distributorId
     * @param  \Carbon\Carbon  $startDate
     * @return \Illuminate\Support\Collection
     */
    private function getOrderReport($distributorId, $startDate)
    {
        return Order::where('distributor_id', $distributorId)
            ->where('created_at', '>=', $startDate)
            ->with(['user', 'orderDetails.product', 'returnRequests', 'payment'])
            ->get();
    }

    /**
     * Generate delivery report data grouped by trucks.
     *
     * @param  int  $distributorId
     * @param  \Carbon\Carbon  $startDate
     * @return \Illuminate\Support\Collection
     */
    private function getDeliveryReport($distributorId, $startDate)
    {
        return \App\Models\Trucks::where('distributor_id', $distributorId)
            ->with(['deliveries' => function ($query) use ($startDate) {
                $query->where('deliveries.created_at', '>=', $startDate)
                    ->with('order.user');
            }])
            ->get();
    }

    /**
     * Generate revenue report data.
     *
     * @param  int  $distributorId
     * @param  \Carbon\Carbon  $startDate
     * @return \Illuminate\Support\Collection
     */
    private function getRevenueReport($distributorId, $startDate)
    {
        return DB::table('earnings')
            ->join('payments', 'earnings.payment_id', '=', 'payments.id')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('earnings.distributor_id', $distributorId)
            ->where('earnings.created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(earnings.created_at) as date'),
                DB::raw('SUM(earnings.amount) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as orders_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get top selling products for the period.
     *
     * @param  int  $distributorId
     * @param  \Carbon\Carbon  $startDate
     * @return \Illuminate\Support\Collection
     */
    private function getTopSellingProducts($distributorId, $startDate)
    {
        return DB::table('products')
            ->join('order_details', 'products.id', '=', 'order_details.product_id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('products.distributor_id', $distributorId)
            ->where('orders.status', 'completed')
            ->where('orders.created_at', '>=', $startDate)
            ->select(
                'products.id',
                'products.product_name',
                'products.price',
                DB::raw('SUM(order_details.quantity) as total_sold'),
                DB::raw('SUM(order_details.quantity * order_details.price) as total_revenue')
            )
            ->groupBy('products.id', 'products.product_name', 'products.price')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();
    }

    /**
     * Display inventory reports.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function inventory(Request $request)
    {
        $distributor_id = Auth::user()->distributor->id;
        $period = $request->input('period', 'weekly');

        // Determine date range based on selected period
        $startDate = $this->getStartDate($period);

        // Get inventory data
        $inventory = $this->getInventoryReport($distributor_id, $startDate);

        // For chart data - prepare for stock in/out by date
        $chartData = $this->prepareInventoryChartData($inventory, $period);

        // Handle PDF export if requested
        if ($request->has('export') && $request->input('export') === 'pdf') {
            return $this->generatePdf('distributors.reports.pdf.inventory', [
                'inventory' => $inventory,
                'period' => $period,
                'startDate' => $startDate,
            ], 'Inventory_Report_' . date('Y-m-d'));
        }

        return view('distributors.reports.inventory', [
            'inventory' => $inventory,
            'chartData' => $chartData,
            'period' => $period,
        ]);
    }

    /**
     * Generate inventory report data.
     *
     * @param  int  $distributorId
     * @param  \Carbon\Carbon  $startDate
     * @return \Illuminate\Support\Collection
     */
    private function getInventoryReport($distributorId, $startDate)
    {
        return Stock::whereHas('product', function ($query) use ($distributorId) {
            $query->where('distributor_id', $distributorId);
        })
            ->with(['product', 'batch', 'user'])
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Prepare chart data for inventory report.
     *
     * @param  \Illuminate\Support\Collection  $inventory
     * @param  string  $period
     * @return array
     */
    private function prepareInventoryChartData($inventory, $period)
    {
        $labels = [];
        $stockInData = [];
        $stockOutData = [];

        if ($period === 'weekly') {
            // Group by day of week
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->format('D');
                $dateStr = $date->format('Y-m-d');

                $stockInData[] = $inventory
                    ->where('type', 'in')
                    ->filter(function ($item) use ($dateStr) {
                        return $item->created_at->format('Y-m-d') === $dateStr;
                    })
                    ->sum('quantity');

                $stockOutData[] = $inventory
                    ->where('type', 'out')
                    ->filter(function ($item) use ($dateStr) {
                        return $item->created_at->format('Y-m-d') === $dateStr;
                    })
                    ->sum('quantity');
            }
        } elseif ($period === 'monthly') {
            // Group by week of month
            $weeks = [];
            $currentDay = Carbon::now();

            for ($i = 0; $i < 4; $i++) {
                $weekStart = $currentDay->copy()->subWeeks($i)->startOfWeek();
                $weekEnd = $currentDay->copy()->subWeeks($i)->endOfWeek();
                $weekLabel = $weekStart->format('M d') . ' - ' . $weekEnd->format('M d');

                $labels[] = $weekLabel;
                $weeks[] = [
                    'start' => $weekStart,
                    'end' => $weekEnd,
                    'label' => $weekLabel
                ];
            }

            $labels = array_reverse($labels);
            $weeks = array_reverse($weeks);

            foreach ($weeks as $week) {
                $stockInData[] = $inventory
                    ->where('type', 'in')
                    ->filter(function ($item) use ($week) {
                        return $item->created_at >= $week['start'] && $item->created_at <= $week['end'];
                    })
                    ->sum('quantity');

                $stockOutData[] = $inventory
                    ->where('type', 'out')
                    ->filter(function ($item) use ($week) {
                        return $item->created_at >= $week['start'] && $item->created_at <= $week['end'];
                    })
                    ->sum('quantity');
            }
        } else {
            // Group by month
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $labels[] = $date->format('M Y');
                $monthYear = $date->format('Y-m');

                $stockInData[] = $inventory
                    ->where('type', 'in')
                    ->filter(function ($item) use ($monthYear) {
                        return $item->created_at->format('Y-m') === $monthYear;
                    })
                    ->sum('quantity');

                $stockOutData[] = $inventory
                    ->where('type', 'out')
                    ->filter(function ($item) use ($monthYear) {
                        return $item->created_at->format('Y-m') === $monthYear;
                    })
                    ->sum('quantity');
            }
        }

        return [
            'labels' => $labels,
            'stockIn' => $stockInData,
            'stockOut' => $stockOutData
        ];
    }

    /**
     * Generate PDF for reports.
     *
     * @param  string  $view
     * @param  array  $data
     * @param  string  $filename
     * @return \Illuminate\Http\Response
     */
    private function generatePdf($view, $data, $filename)
    {
        $pdf = PDF::loadView($view, $data);
        return $pdf->download($filename . '.pdf');
    }
}
