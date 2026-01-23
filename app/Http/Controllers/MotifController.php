<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMotifRequest;
use App\Http\Requests\UpdateMotifRequest;
use App\Http\Resources\MotifResource;
use App\Models\Motif;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as Status;

class MotifController extends Controller
{
    public function index(): JsonResponse
    {
        return MotifResource::collection(Motif::all())->response();
    }

    public function show(Motif $motif): JsonResponse
    {
        return response()->json(['data' => new MotifResource($motif)]);
    }

    public function store(StoreMotifRequest $request): JsonResponse
    {
        $this->authorize('create', Motif::class);

        $motif = Motif::create($request->validated());

        return response()->json([
            'message' => 'Motif created successfully.',
            'data' => new MotifResource($motif),
        ], Status::HTTP_CREATED);
    }

    public function update(UpdateMotifRequest $request, Motif $motif): JsonResponse
    {
        $this->authorize('update', $motif);

        $motif->update($request->validated());

        return response()->json([
            'message' => 'Motif updated successfully.',
            'data' => new MotifResource($motif),
        ]);
    }

    public function destroy(Motif $motif): JsonResponse
    {
        $this->authorize('delete', $motif);

        $motif->delete();

        return response()->json([
            'message' => 'Motif deleted successfully.',
        ]);
    }
}
