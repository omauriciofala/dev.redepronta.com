<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use App\Models\Gender;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GeoController extends Controller
{
    public function states(): JsonResponse
    {
        $states = State::orderBy('name')->get(['id', 'code', 'name']);
        return response()->json(['data' => $states]);
    }

    public function cities(Request $request): JsonResponse
    {
        $query = City::with('state:id,code,name');

        // Busca direta por ID específico
        if ($request->filled('id')) {
            $city = $query->find($request->input('id'));
            return response()->json(['data' => $city ? [$city] : []]);
        }

        // Busca por termo (nome ou código IBGE)
        if ($request->filled('q')) {
            $term = trim($request->input('q'));
            if (mb_strlen($term) >= 3) {
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "{$term}%")
                      ->orWhere('name', 'like', "%{$term}%")
                      ->orWhere('ibge_code', 'like', "{$term}%");
                });
            } else {
                return response()->json(['data' => []]);
            }
        }

        if ($request->filled('state_id')) {
            $query->where('state_id', $request->input('state_id'));
        }

        // Limite ergonômico de resultados para navegação ágil
        $cities = $query->orderBy('name')->limit(30)->get(['id', 'state_id', 'ibge_code', 'name']);

        return response()->json(['data' => $cities]);
    }

    public function genders(): JsonResponse
    {
        $genders = Gender::orderBy('name')->get(['id', 'code', 'name']);
        return response()->json(['data' => $genders]);
    }
}
