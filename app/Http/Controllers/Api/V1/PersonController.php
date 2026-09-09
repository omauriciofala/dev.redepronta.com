<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Person\StorePersonRequest;
use App\Http\Requests\Person\UpdatePersonRequest;
use App\Http\Resources\Person\PersonResource;
use App\Models\Person;
use App\Services\Person\PersonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PersonController extends Controller
{
    public function __construct(
        protected PersonService $personService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only([
            'search', 'status', 'person_type', 'city_id', 'state_id', 'state_code',
            'group_id', 'group_name', 'group', 'persona', 'sort_by', 'sort_direction'
        ]);

        $people = $this->personService->list(
            $filters,
            $request->integer('per_page', 15)
        );

        $counts = $this->personService->getCounts();

        return PersonResource::collection($people)->additional([
            'meta' => [
                'counts' => $counts,
            ],
        ]);
    }

    public function store(StorePersonRequest $request): JsonResponse
    {
        $person = $this->personService->create($request->validated());
        $person->load(['city.state', 'gender', 'group']);

        return (new PersonResource($person))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Person $person): PersonResource
    {
        $person->load(['city.state', 'gender', 'group']);
        return new PersonResource($person);
    }

    public function update(UpdatePersonRequest $request, Person $person): PersonResource
    {
        $updated = $this->personService->update($person, $request->validated());
        return new PersonResource($updated);
    }

    public function destroy(Person $person): JsonResponse
    {
        $this->personService->delete($person);

        return response()->json([
            'success' => true,
            'message' => 'Pessoa removida com sucesso.',
        ]);
    }

    public function toggleStatus(Person $person): PersonResource
    {
        $updated = $this->personService->toggleStatus($person);
        $updated->load(['city.state', 'gender', 'group']);
        return new PersonResource($updated);
    }
}
