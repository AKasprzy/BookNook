<?php

namespace App\Http\Controllers;

use App\Enums\BookStatus;
use App\Http\Requests\StoreShelveRequest;
use App\Http\Requests\UpdateShelveRequest;
use App\Http\Resources\ShelveResource;
use App\Models\Shelve;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as Status;

class ShelveController extends Controller
{
    public function index(): JsonResponse
    {
        $shelves = Shelve::with('bookEdition')->paginate(request('per_page', 10));

        return ShelveResource::collection($shelves)->response();
    }

    public function show(Shelve $shelve): JsonResponse
    {
        $shelve->load('bookEdition');

        return ShelveResource::make($shelve)->response();
    }

    public function store(StoreShelveRequest $request): JsonResponse
    {
        $shelve = Shelve::create([
            'user_id' => $request->user()->id,
            ...$request->validated(),
        ]);

        return ShelveResource::make($shelve->load('bookEdition'))
            ->additional(['message' => 'Shelve created successfully.'])
            ->response()
            ->setStatusCode(Status::HTTP_CREATED);
    }

    public function update(UpdateShelveRequest $request, Shelve $shelve): JsonResponse
    {
        $this->authorize('update', $shelve);

        $shelve->update($request->validated());

        return ShelveResource::make($shelve->load('bookEdition'))
            ->additional(['message' => 'Shelve updated successfully.'])
            ->response();
    }

    public function destroy(Shelve $shelve): JsonResponse
    {
        $this->authorize('delete', $shelve);

        $shelve->delete();

        return response()->json(['message' => 'Shelve deleted successfully.']);
    }

    public function myEditions(): JsonResponse
    {
        $shelves = Shelve::with('bookEdition')
            ->where('user_id', request()->user()->id)
            ->paginate(request('per_page', 10));

        return ShelveResource::collection($shelves)->response();
    }

    public function myEditionsByStatus(BookStatus $status): JsonResponse
    {
        $shelves = Shelve::with('bookEdition')
            ->where('user_id', request()->user()->id)
            ->where('status', $status->value)
            ->paginate(request('per_page', 10));

        return ShelveResource::collection($shelves)->response();
    }
}
