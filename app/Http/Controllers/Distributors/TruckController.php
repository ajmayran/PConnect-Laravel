<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Trucks;
use App\Models\Delivery;
use Illuminate\Http\Request;
use App\Models\DeliveryLocations;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TruckController extends Controller
{
    public function index()
    {
        $trucks = Trucks::where('distributor_id', Auth::user()->distributor->id)
            ->with('deliveryLocations')  // Only load the deliveryLocations relationship
            ->withCount(['deliveries as deliveries_count' => function ($query) {
                $query->whereIn('status', ['pending', 'processing', 'in_transit']);
            }])
            ->get();

        return view('distributors.trucks.index', compact('trucks'));
    }

    public function show(Trucks $truck)
    {
        $deliveries = $truck->deliveries()
            ->with([
                'order',
                'order.user',
                'order.orderDetails',
                'order.orderDetails.product'
            ])
            ->where('status', '!=', 'delivered')
            ->latest('truck_delivery.started_at')
            ->paginate(10);

        return view('distributors.trucks.show', compact('truck', 'deliveries'));
    }

    public function edit($id)
    {
        $truck = Trucks::with('deliveryLocations')->findOrFail($id);

        $locations = $truck->deliveryLocations->map(function ($location) {
            return [
                'id' => $location->id,
                'barangay' => $location->barangay,
                'street' => $location->street,
                'region' => $location->region,
                'province' => $location->province,
                'city' => $location->city,
            ];
        });

        return response()->json([
            'plate_number' => $truck->plate_number,
            'status' => $truck->status,
            'locations' => $locations,
        ]);
    }

    public function locations($id)
    {
        $truck = Trucks::with('deliveryLocations')->findOrFail($id);

        $locations = $truck->deliveryLocations->map(function ($location) {
            return [
                'id' => $location->id,
                'barangay' => $location->barangay,
                'street' => $location->street,
                'barangay_name' => $location->barangayName,
            ];
        });

        return response()->json($locations);
    }


    public function update(Request $request, Trucks $truck)
    {
        $validated = $request->validate([
            'plate_number' => 'required|unique:trucks,plate_number,' . $truck->id,
            'status' => 'required|in:available,on_delivery,maintenance',
            'locations' => 'required|array|min:1',
            'locations.*.barangay' => 'required',
            'locations.*.street' => 'required',
            'locations.*.region' => 'required',
            'locations.*.province' => 'required',
            'locations.*.city' => 'required',
        ]);

        // Update the truck info
        $truck->plate_number = $validated['plate_number'];
        $truck->status = $validated['status'];
        $truck->save();

        // Get existing location IDs
        $existingLocationIds = $truck->deliveryLocations->pluck('id')->toArray();
        $updatedLocationIds = [];

        // Update or create locations
        foreach ($validated['locations'] as $locationData) {
            if (isset($locationData['id']) && $locationData['id']) {
                // Update existing location
                $location = DeliveryLocations::find($locationData['id']);
                if ($location) {
                    $location->update([
                        'barangay' => $locationData['barangay'],
                        'street' => $locationData['street'],
                        'region' => $locationData['region'],
                        'province' => $locationData['province'],
                        'city' => $locationData['city'],
                    ]);
                    $updatedLocationIds[] = $location->id;
                }
            } else {
                // Create new location
                $location = new DeliveryLocations([
                    'barangay' => $locationData['barangay'],
                    'street' => $locationData['street'],
                    'region' => $locationData['region'],
                    'province' => $locationData['province'],
                    'city' => $locationData['city'],
                    'truck_id' => $truck->id,
                ]);
                $location->save();
                $updatedLocationIds[] = $location->id;
            }
        }

        // Delete any locations that weren't updated or created
        $locationsToDelete = array_diff($existingLocationIds, $updatedLocationIds);
        if (!empty($locationsToDelete)) {
            DeliveryLocations::whereIn('id', $locationsToDelete)->delete();
        }

        return redirect()->route('distributors.trucks.index')
            ->with('success', 'Truck updated successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|unique:trucks',
            'locations' => 'required|array|min:1',
            'locations.*.barangay' => 'required',
            'locations.*.street' => 'required',
            'locations.*.region' => 'required',
            'locations.*.province' => 'required',
            'locations.*.city' => 'required',
        ]);

        // Create truck first
        $truck = new Trucks();
        $truck->distributor_id = Auth::user()->distributor->id;
        $truck->plate_number = $validated['plate_number'];
        $truck->status = 'available';
        $truck->save();

        // Create all locations
        foreach ($validated['locations'] as $locationData) {
            DeliveryLocations::create([
                'barangay' => $locationData['barangay'],
                'street' => $locationData['street'],
                'region' => $locationData['region'],
                'province' => $locationData['province'],
                'city' => $locationData['city'],
                'truck_id' => $truck->id,
            ]);
        }

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

    public function outForDelivery(Trucks $truck)
    {
        // Check if the truck is available
        if ($truck->status !== 'available') {
            return back()->with('error', 'This truck is not currently available for delivery');
        }

        // Get all active deliveries for this truck
        $deliveries = $truck->deliveries()
            ->whereIn('status', ['in_transit', 'pending'])
            ->get();

        if ($deliveries->isEmpty()) {
            return back()->with('error', 'No active deliveries found for this truck');
        }

        // Update all deliveries to "out_for_delivery" status
        foreach ($deliveries as $delivery) {
            $delivery->update(['status' => 'out_for_delivery']);
        }

        // Update truck status to "on_delivery"
        $truck->update(['status' => 'on_delivery']);

        return back()->with('success', 'All deliveries are now out for delivery and truck status updated');
    }

    public function deliveryHistory(Trucks $truck, Request $request)
    {
        // Verify the truck belongs to the current distributor
        if ($truck->distributor_id !== Auth::user()->distributor->id) {
            abort(403, 'Unauthorized action.');
        }

        // Start with a base query for completed deliveries
        $query = Delivery::whereHas('trucks', function ($query) use ($truck) {
            $query->where('trucks.id', $truck->id);
        })
            ->with([
                'order.user',
                'order.orderDetails.product',
            ])
            ->latest('updated_at');

        // Apply status filter
        if ($request->filter === 'delivered') {
            $query->where('status', 'delivered');
        } elseif ($request->filter === 'failed') {
            $query->where('status', 'failed');
        } else {
            $query->whereIn('status', ['delivered', 'failed']);
        }

        // Apply date range filter
        if ($request->period === 'today') {
            $query->whereDate('updated_at', now()->toDateString());
        } elseif ($request->period === 'week') {
            $query->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($request->period === 'month') {
            $query->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year);
        }

        $deliveryHistory = $query->paginate(10)->withQueryString();

        return view('distributors.trucks.delivery-history', compact('truck', 'deliveryHistory'));
    }
}
