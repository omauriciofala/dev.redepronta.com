<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\StoreMaterialOwnerRequest;
use App\Models\MaterialOwner;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaterialOwnerController extends Controller
{
    protected function getAccountId(): int
    {
        return session('active_account_id') ?? \App\Models\Account::first()?->id ?? 1;
    }

    public function index(Request $request): JsonResponse
    {
        $accountId = $this->getAccountId();
        $query = MaterialOwner::where('account_id', $accountId)->with('person');

        if ($request->filled('search')) {
            $s = $request->query('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhereHas('person', function ($pq) use ($s) {
                      $pq->where('name', 'like', "%{$s}%")
                         ->orWhere('document_number', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->boolean('active_only', false)) {
            $query->where('is_active', true);
        }

        $owners = $query->orderBy('name')->get();

        return response()->json([
            'data' => $owners,
        ]);
    }

    public function store(StoreMaterialOwnerRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['account_id'] = $this->getAccountId();

        // Garante que a pessoa vinculada possua o papel de solicitante
        $person = Person::findOrFail($data['person_id']);
        if (!$person->is_requester) {
            $person->update(['is_requester' => true]);
        }

        $owner = MaterialOwner::create($data);
        $owner->load('person');

        return response()->json([
            'message' => 'Proprietário cadastrado com sucesso.',
            'data' => $owner,
        ], 201);
    }

    public function show(MaterialOwner $materialOwner): JsonResponse
    {
        $materialOwner->load('person');
        return response()->json(['data' => $materialOwner]);
    }

    public function update(StoreMaterialOwnerRequest $request, MaterialOwner $materialOwner): JsonResponse
    {
        $data = $request->validated();

        // Garante que a pessoa vinculada possua o papel de solicitante
        $person = Person::findOrFail($data['person_id']);
        if (!$person->is_requester) {
            $person->update(['is_requester' => true]);
        }

        $materialOwner->update($data);
        $materialOwner->load('person');

        return response()->json([
            'message' => 'Proprietário atualizado com sucesso.',
            'data' => $materialOwner,
        ]);
    }

    public function destroy(MaterialOwner $materialOwner): JsonResponse
    {
        $materialOwner->delete();

        return response()->json([
            'message' => 'Proprietário excluído com sucesso.',
        ]);
    }
}
