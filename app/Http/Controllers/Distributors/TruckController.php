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
            ->with(['order.user', 'order.orderDetails'])
            ->latest('truck_delivery.started_at')
            ->get();

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
}
