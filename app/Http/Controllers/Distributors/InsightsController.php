<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Earning;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class InsightsController extends Controller
{
    public function index()
    {
        $distributor_id = Auth::user()->distributor->id;

        // Get earnings data with relationships
        $earnings = Earning::where('distributor_id', $distributor_id)
            ->with(['payment.order.user', 'payment.order.orderDetails'])
            ->latest();

        // Calculate total earnings from the earnings table
        $totalEarnings = $earnings->sum('amount');

        // Get monthly earnings for the chart
        $monthlyEarnings = Earning::where('distributor_id', $distributor_id)
            ->selectRaw('SUM(amount) as total, MONTH(created_at) as month')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->get();

        // Get recent earnings
        $recentEarnings = $earnings->take(5)->get();

        return view('distributors.insights.index', compact(
            'totalEarnings',
            'monthlyEarnings',
            'recentEarnings'
        ));
    }
}
