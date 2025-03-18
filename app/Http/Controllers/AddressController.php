<?php

namespace App\Http\Controllers;

use Yajra\Address\Entities\City;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\Address\Entities\Region;
use Illuminate\Support\Facades\Log;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\Province;

class AddressController extends Controller
{
    public function getRegions()
    {
        $regions = Region::orderBy('name')->get(['code', 'name']);
        return response()->json($regions);
    }

    public function getProvinces($regionCode)
    {
        $provinces = Province::where('region_id', $regionCode)
            ->orderBy('name')
            ->get(['code', 'name']);
        return response()->json($provinces);
    }

    public function getCities($provinceCode)
    {
        $cities = City::where('province_id', $provinceCode)
            ->orderBy('name')
            ->get(['code', 'name']);
        return response()->json($cities);
    }

    public function getBarangays($cityCode)
    {
        $barangays = Barangay::where('city_id', $cityCode)
            ->orderBy('name')
            ->get(['code', 'name']);

        // If no barangays found and it's Zamboanga City, try another approach
        if ($barangays->count() == 0 && ($cityCode == '093170' || $cityCode == '09317')) {
            // Try both possible formats for city_id
            $barangays = Barangay::where(function($query) {
                $query->where('city_id', '093170')
                      ->orWhere('city_id', '09317');
            })
            ->orderBy('name')
            ->get(['code', 'name']);
            
            // Log the result for debugging
            Log::info("Searched for Zamboanga barangays, found: " . $barangays->count());
        }

        return response()->json($barangays);
    }

    public function getCitiesForProvince($provinceCode)
    {
        // Get regular cities within the province
        $cities = City::where('province_id', $provinceCode)
            ->orderBy('name')
            ->get(['code', 'name']);

        // If it's Zamboanga del Sur (097300), also include Zamboanga City
        if ($provinceCode == '097300' || $provinceCode == '09730') {
            $zamboangaCity = City::where('code', '093170')->first(['code', 'name']);
            if ($zamboangaCity) {
                $cities->push($zamboangaCity);
            }
        }

        return response()->json($cities);
    }

    // Add a debugging endpoint to check what's in the database
    public function debugZamboangaData()
    {
        $result = [
            'city' => City::where('name', 'like', '%Zamboanga%')->get(['code', 'name', 'city_id', 'province_id']),
            'barangays_count' => Barangay::where('city_id', '093170')->count(),
            'barangays_sample' => Barangay::where('city_id', '093170')->take(5)->get(['code', 'name', 'city_id'])
        ];
        
        return response()->json($result);
    }
}