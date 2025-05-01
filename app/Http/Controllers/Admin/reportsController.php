<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order; // Import the Order model
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DistributorSubscription;
class reportsController extends Controller
{

    public function reports(Request $request)
    {
        $timeRange = $request->input('time_range', '1_month'); // Default to 1 month
        $view = $request->input('view', 'distributors'); // Default to 'distributors' view

        // Determine the start date based on the selected time range
        $startDate = match ($timeRange) {
            '24_hours' => now()->subDay(),
            '1_week' => now()->subWeek(),
            '1_month' => now()->subMonth(),
            '1_year' => now()->subYear(),
            default => now()->subMonth(),
        };

        // Fetch the reports data
        $reports = Order::selectRaw('
            orders.distributor_id,
            COUNT(orders.id) as total_orders,
            SUM(order_details.quantity) as total_products_sold,
            SUM(order_details.subtotal) as total_revenue
        ')
        ->join('order_details', 'orders.id', '=', 'order_details.order_id')
        ->where('orders.created_at', '>=', $startDate)
        ->groupBy('orders.distributor_id')
        ->with(['distributor']) // Eager-load distributor relationship
        ->paginate(10); // Paginate results

        // Fetch subscription stats
        $stats = [
            'totalSubscribers' => DistributorSubscription::count(),
            'revenue' => DistributorSubscription::sum('amount'),
        ];

        // Pass the variables to the view
        return view('admin.reports.index', compact('reports', 'timeRange', 'stats', 'view'));
    }

public function downloadPdf(Request $request)
{
    $timeRange = $request->input('time_range', '1_month'); // Default to 1 month

    // Determine the start date based on the selected time range
    $startDate = match ($timeRange) {
        '24_hours' => now()->subDay(),
        '1_week' => now()->subWeek(),
        '1_month' => now()->subMonth(),
        '1_year' => now()->subYear(),
        default => now()->subMonth(),
    };

    // Fetch the reports data
    $reports = Order::selectRaw('
        orders.distributor_id,
        COUNT(orders.id) as total_orders,
        SUM(order_details.quantity) as total_products_sold,
        SUM(order_details.subtotal) as total_revenue
    ')
    ->join('order_details', 'orders.id', '=', 'order_details.order_id')
    ->where('orders.created_at', '>=', $startDate)
    ->groupBy('orders.distributor_id')
    ->with(['distributor']) // Eager-load distributor relationship
    ->get();

    // Generate the PDF
    $pdf = Pdf::loadView('admin.reports.pdfButton', compact('reports', 'timeRange'));

    // Return the PDF for download
    return $pdf->download('system_reports.pdf');
}

}