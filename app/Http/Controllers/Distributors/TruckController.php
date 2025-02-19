<?php

namespace App\Http\Controllers\Distributors;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Trucks;
use App\Models\Delivery;

class TruckController extends Controller
{
    public function index()
    {
        $trucks = Trucks::where('distributor_id', Auth::user()->distributor->id)
            ->withCount(['deliveries' => function ($query) {
                $query->whereIn('status', ['in_transit', 'out_for_delivery']);
            }])
            ->get();

        return view('distributors.trucks.index', compact('trucks'));
    }

    public function show(Trucks $truck)
    {
        $deliveries = $truck->deliveries()
            ->with(['order.user', 'order.orderDetails'])
            ->latest('truck_delivery.started_at')
            ->get();

        return view('distributors.trucks.show', compact('truck', 'deliveries'));
    }

    public function edit(Trucks $truck)
    {
        return response()->json($truck);
    }

    public function update(Request $request, Trucks $truck)
    {
        $validated = $request->validate([
            'plate_number' => 'required|unique:trucks,plate_number,' . $truck->id,
            'delivery_location' => 'nullable|string',
            'status' => 'required|in:available,on_delivery,maintenance',
        ]);

        $truck->update($validated);

        return redirect()->route('distributors.trucks.index')
            ->with('success', 'Truck updated successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|unique:trucks',
            'delivery_location' => 'nullable|string',
        ]);

        $truck = new Trucks($validated);
        $truck->distributor_id = Auth::user()->distributor->id;
        $truck->status = 'available';
        $truck->save();

        return redirect()->route('distributors.trucks.index')
            ->with('success', 'Truck added successfully');
    }

    public function destroy(Trucks $truck)
    {
        $truck->delete();
        return redirect()->route('distributors.trucks.index')
            ->with('success', 'Truck removed successfully');
    }

    public function assignDelivery(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'truck_id' => 'required|exists:trucks,id'
        ]);

        $truck = Trucks::find($validated['truck_id']);

        if ($truck->status !== 'available') {
            return back()->with('error', 'Selected truck is not available');
        }

        $delivery->trucks()->attach($truck->id, [
            'started_at' => now()
        ]);

        $delivery->update(['status' => 'in_transit']);

        return redirect()->back()->with('success', 'Delivery assigned to truck successfully');
    }
}
