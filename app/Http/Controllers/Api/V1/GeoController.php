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
        if ($request->filled('q') || $request->filled('search')) {
            $term = trim($request->input('search') ?? $request->input('q'));
            if (!empty($term)) {
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                      ->orWhere('ibge_code', 'like', "%{$term}%");
                });
            }
        }

        if ($request->filled('state_id')) {
            $query->where('state_id', $request->input('state_id'));
        }

        // Paginação oficial do Laravel
        if ($request->boolean('paginate') || $request->has('page')) {
            $perPage = $request->integer('per_page', 15);
            $paginated = $query->orderBy('name')->paginate($perPage, ['id', 'state_id', 'ibge_code', 'name']);
            return response()->json([
                'data' => $paginated->items(),
                'meta' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'per_page' => $paginated->perPage(),
                    'total' => $paginated->total(),
                    'from' => $paginated->firstItem(),
                    'to' => $paginated->lastItem(),
                ]
            ]);
        }

        // Limite ergonômico de resultados ou carga completa via all=1
        if ($request->boolean('all')) {
            $cities = $query->orderBy('name')->get(['id', 'state_id', 'ibge_code', 'name']);
        } else {
            $limit = $request->integer('limit', 30);
            $cities = $query->orderBy('name')->limit($limit)->get(['id', 'state_id', 'ibge_code', 'name']);
        }

        return response()->json(['data' => $cities]);
    }

    public function genders(): JsonResponse
    {
        $genders = Gender::orderBy('name')->get(['id', 'code', 'name']);
        return response()->json(['data' => $genders]);
    }
    public function cep(string $postalCode): JsonResponse
    {
        $cleanCep = preg_replace('/\D/', '', $postalCode);

        if (strlen($cleanCep) !== 8) {
            return response()->json([
                'success' => false,
                'message' => 'CEP inválido. Forneça 8 dígitos numéricos.',
            ], 422);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get("https://viacep.com.br/ws/{$cleanCep}/json/");

            if (!$response->successful() || isset($response->json()['erro'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'CEP não encontrado na base do ViaCEP.',
                ], 404);
            }

            $data = $response->json();

            // Localiza a cidade canônica pelo código IBGE oficial
            $city = null;
            if (!empty($data['ibge'])) {
                $city = City::with('state:id,code,name')->where('ibge_code', $data['ibge'])->first();
            }

            // Fallback por nome da localidade e UF
            if (!$city && !empty($data['localidade']) && !empty($data['uf'])) {
                $city = City::with('state:id,code,name')
                    ->where('name', 'like', $data['localidade'])
                    ->whereHas('state', fn ($q) => $q->where('code', $data['uf']))
                    ->first();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'postal_code' => $data['cep'] ?? $postalCode,
                    'street' => $data['logradouro'] ?? '',
                    'complement' => $data['complemento'] ?? '',
                    'neighborhood' => $data['bairro'] ?? '',
                    'city_id' => $city?->id,
                    'city_name' => $city?->name ?? $data['localidade'] ?? '',
                    'state_code' => $city?->state?->code ?? $data['uf'] ?? '',
                    'ibge_code' => $city?->ibge_code ?? $data['ibge'] ?? '',
                    'city' => $city,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Falha de comunicação com o serviço ViaCEP: ' . $e->getMessage(),
            ], 502);
        }
    }

    public function storeState(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|size:2|unique:states,code',
            'name' => 'required|string|max:50',
        ]);

        $state = State::create([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado cadastrado com sucesso.',
            'data' => $state,
        ], 201);
    }

    public function storeCity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'state_id' => 'required|exists:states,id',
            'name' => 'required|string|max:100',
            'ibge_code' => 'required|string|size:7|unique:cities,ibge_code',
        ]);

        $city = City::create($validated);
        $city->load('state:id,code,name');

        return response()->json([
            'success' => true,
            'message' => 'Município cadastrado com sucesso.',
            'data' => $city,
        ], 201);
    }

    public function updateCity(Request $request, City $city): JsonResponse
    {
        $validated = $request->validate([
            'state_id' => 'sometimes|required|exists:states,id',
            'name' => 'sometimes|required|string|max:100',
            'ibge_code' => 'sometimes|required|string|size:7|unique:cities,ibge_code,' . $city->id,
        ]);

        $city->update($validated);
        $city->load('state:id,code,name');

        return response()->json([
            'success' => true,
            'message' => 'Município atualizado com sucesso.',
            'data' => $city,
        ]);
    }

    public function updateState(Request $request, State $state): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'sometimes|required|string|size:2|unique:states,code,' . $state->id,
            'name' => 'sometimes|required|string|max:50',
        ]);

        if (isset($validated['code'])) {
            $validated['code'] = strtoupper($validated['code']);
        }

        $state->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Estado atualizado com sucesso.',
            'data' => $state,
        ]);
    }

    public function destroyState(State $state): JsonResponse
    {
        if ($state->cities()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir este Estado pois existem municípios vinculados a ele.',
            ], 422);
        }

        $state->delete();

        return response()->json([
            'success' => true,
            'message' => 'Estado excluído com sucesso.',
        ]);
    }

    public function destroyCity(City $city): JsonResponse
    {
        if ($city->people()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir este Município pois existem pessoas vinculadas a ele.',
            ], 422);
        }

        $city->delete();

        return response()->json([
            'success' => true,
            'message' => 'Município excluído com sucesso.',
        ]);
    }
}
