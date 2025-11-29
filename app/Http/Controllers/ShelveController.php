<?php

namespace App\Http\Controllers;

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
        $shelves = Shelve::with('book')->paginate(request('per_page', 10));

        return ShelveResource::collection($shelves)->response();
    }

    public function show(Shelve $shelve): JsonResponse
    {
        $shelve->load('book');

        return ShelveResource::make($shelve)->response();
    }

    public function store(StoreShelveRequest $request): JsonResponse
    {
        $shelve = Shelve::create([
            'user_id' => $request->user()->id,
            ...$request->validated(),
        ]);

        return ShelveResource::make($shelve->load('book'))
            ->additional(['message' => 'Shelve created successfully.'])
            ->response()
            ->setStatusCode(Status::HTTP_CREATED);
    }

    public function update(UpdateShelveRequest $request, Shelve $shelve): JsonResponse
    {
        $shelve->update($request->validated());

        return ShelveResource::make($shelve->load('book'))
            ->additional(['message' => 'Shelve updated successfully.'])
            ->response();
    }

    public function destroy(Shelve $shelve): JsonResponse
    {
        $shelve->delete();

        return response()->json(['message' => 'Shelve deleted successfully.']);
    }
}
