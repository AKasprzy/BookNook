<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Http\Resources\GenreResource;
use App\Models\Genre;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as Status;

class GenreController extends Controller
{
    use AuthorizesRequests;

    public function index(): JsonResponse
    {
        return GenreResource::collection(Genre::all())->response();
    }

    public function show(Genre $genre): JsonResponse
    {
        return response()->json(['data' => new GenreResource($genre)]);
    }

    public function store(StoreGenreRequest $request): JsonResponse
    {
        $this->authorize('create', Genre::class);

        $genre = Genre::create($request->validated());

        return response()->json([
            'message' => 'Genre created successfully.',
            'data' => new GenreResource($genre),
        ], Status::HTTP_CREATED);
    }

    public function update(UpdateGenreRequest $request, Genre $genre): JsonResponse
    {
        $this->authorize('update', $genre);

        $genre->update($request->validated());

        return response()->json([
            'message' => 'Genre updated successfully.',
            'data' => new GenreResource($genre),
        ]);
    }

    public function destroy(Genre $genre): JsonResponse
    {
        $this->authorize('delete', $genre);

        $genre->delete();

        return response()->json([
            'message' => 'Genre deleted successfully.',
        ]);
    }
}
