<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DistributorSubscription;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = DistributorSubscription::with(['distributor.user']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $subscriptions = $query->latest()->paginate(15);
        
        $stats = [
            'total' => DistributorSubscription::count(),
            'active' => DistributorSubscription::where('status', 'active')
                ->where('expires_at', '>', now())->count(),
            'expired' => DistributorSubscription::where('status', 'expired')
                ->orWhere(function($q) {
                    $q->where('status', 'active')
                      ->where('expires_at', '<=', now());
                })->count(),
            'pending' => DistributorSubscription::where('status', 'pending')->count(),
            'failed' => DistributorSubscription::where('status', 'failed')->count(),
        ];
        
        return view('admin.subscriptions.index', compact('subscriptions', 'stats', 'status'));
    }
    
    public function show($id)
    {
        $subscription = DistributorSubscription::with(['distributor.user'])->findOrFail($id);
        return view('admin.subscriptions.show', compact('subscription'));
    }
    
    public function extend(Request $request, $id)
    {
        $request->validate([
            'months' => 'required|integer|min:1'
        ]);
        
        $subscription = DistributorSubscription::findOrFail($id);
        
        // If expired, restart from today
        $startDate = $subscription->status === 'expired' || 
                     ($subscription->expires_at && $subscription->expires_at->lt(Carbon::now())) 
                     ? Carbon::now() 
                     : $subscription->expires_at;
        
        $expiresAt = $startDate->copy()->addMonths($request->months);
        
        $subscription->update([
            'status' => 'active',
            'expires_at' => $expiresAt,
        ]);
        
        return redirect()->route('admin.subscriptions.show', $subscription->id)
            ->with('success', "Subscription extended by {$request->months} month(s).");
    }
    
    public function cancel($id)
    {
        $subscription = DistributorSubscription::findOrFail($id);
        
        $subscription->update([
            'status' => 'expired',
            'expires_at' => Carbon::now(),
        ]);
        
        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription has been cancelled.');
    }
}