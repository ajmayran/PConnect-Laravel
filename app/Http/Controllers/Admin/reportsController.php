<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order; // Import the Order model

class reportsController extends Controller
{
    public function reports(Request $request)
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

    // Fetch aggregated data
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

    return view('admin.reports.index', compact('reports', 'timeRange'));
}

    public function show($id)
    {
        $report = Order::with(['distributor', 'payments', 'delivery'])
            ->where('id', $id)
            ->firstOrFail();

        return view('admin.reports.show', compact('report'));
    }
    public function downloadReport(Request $request)
    {
        // Implement the logic to download the report as a CSV or Excel file
        // You can use Laravel's built-in export functionality or a package like Maatwebsite Excel
        return response()->download('path/to/report.csv');
    }
    public function filterReports(Request $request)
    {
        // Implement the logic to filter reports based on user input
        // You can use query parameters to filter the reports
        return redirect()->route('admin.reports.index', $request->all());
    }
    public function exportReport(Request $request)
    {
        // Implement the logic to export the report as a CSV or Excel file
        // You can use Laravel's built-in export functionality or a package like Maatwebsite Excel
        return response()->download('path/to/report.csv');
    }
    public function deleteReport($id)
    {
        // Implement the logic to delete a specific report
        // You can use the Order model to delete the report
        $report = Order::findOrFail($id);
        $report->delete();

        return redirect()->route('admin.reports.index')->with('success', 'Report deleted successfully.');
    }
    public function deleteSelectedReports(Request $request)
    {
        // Implement the logic to delete selected reports
        // You can use the Order model to delete the reports
        $reportIds = $request->input('selected_reports', []);
        Order::whereIn('id', $reportIds)->delete();

        return redirect()->route('admin.reports.index')->with('success', 'Selected reports deleted successfully.');
    }
    public function viewReport($id)
    {
        // Implement the logic to view a specific report
        // You can use the Order model to fetch the report details
        $report = Order::with(['distributor', 'payments', 'delivery'])
            ->where('id', $id)
            ->firstOrFail();

        return view('admin.reports.view', compact('report'));
    }
    public function generateReport(Request $request)
    {
        // Implement the logic to generate a report based on user input
        // You can use the Order model to fetch the report data
        return redirect()->route('admin.reports.index')->with('success', 'Report generated successfully.');
    }
    public function downloadSelectedReports(Request $request)
    {
        // Implement the logic to download selected reports as a CSV or Excel file
        // You can use Laravel's built-in export functionality or a package like Maatwebsite Excel
        return response()->download('path/to/report.csv');
    }
    public function sendReport(Request $request)
    {
        // Implement the logic to send the report via email
        // You can use Laravel's built-in mail functionality
        return response()->json(['success' => true, 'message' => 'Report sent successfully.']);
    }
    public function sendSelectedReports(Request $request)
    {
        // Implement the logic to send selected reports via email
        // You can use Laravel's built-in mail functionality
        return response()->json(['success' => true, 'message' => 'Selected reports sent successfully.']);
    }
}