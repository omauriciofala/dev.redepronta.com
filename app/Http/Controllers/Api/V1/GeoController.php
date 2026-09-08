<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Geo\CityResource;
use App\Http\Resources\Geo\StateResource;
use App\Models\City;
use App\Models\Gender;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeoController extends Controller
{
    public function states(): JsonResponse
    {
        $states = State::orderBy('name')->get();
        return response()->json([
            'data' => StateResource::collection($states),
        ]);
    }

    public function cities(Request $request): JsonResponse
    {
        $query = City::with('state');

        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('ibge_code', 'like', "%{$search}%");
            });
        }

        $cities = $query->orderBy('name')->limit(50)->get();

        return response()->json([
            'data' => CityResource::collection($cities),
        ]);
    }

    public function genders(): JsonResponse
    {
        $genders = Gender::orderBy('id')->get();
        return response()->json([
            'data' => $genders,
        ]);
    }
}
