<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Http\Resources\GenreResource;
use App\Models\Genre;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as Status;

class GenreController extends Controller
{
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
        $genre = Genre::create($request->validated());

        return response()->json([
            'message' => 'Genre created successfully.',
            'data' => new GenreResource($genre),
        ], Status::HTTP_CREATED);
    }

    public function update(UpdateGenreRequest $request, Genre $genre): JsonResponse
    {
        $genre->update($request->validated());

        return response()->json([
            'message' => 'Genre updated successfully.',
            'data' => new GenreResource($genre),
        ]);
    }

    public function destroy(Genre $genre): JsonResponse
    {
        $genre->delete();

        return response()->json([
            'message' => 'Genre deleted successfully.',
        ]);
    }
}
