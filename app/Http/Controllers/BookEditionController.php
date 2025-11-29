<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookEditionRequest;
use App\Http\Requests\UpdateBookEditionRequest;
use App\Http\Resources\BookEditionResource;
use App\Models\BookEdition;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as Status;

class BookEditionController extends Controller
{
    public function index(): JsonResponse
    {
        $perPage = request()->integer('per_page', 10);
        $paginated = BookEdition::with('book')->paginate($perPage);

        return BookEditionResource::collection($paginated)->response();
    }

    public function show(BookEdition $bookEdition): JsonResponse
    {
        $bookEdition->load('book');

        return response()->json([
            'data' => new BookEditionResource($bookEdition),
        ]);
    }

    public function store(StoreBookEditionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $bookEdition = BookEdition::create($data);
        $bookEdition->load('book.genres', 'book.motifs');

        return response()->json([
            'message' => 'Book edition created successfully.',
            'data' => new BookEditionResource($bookEdition),
        ], Status::HTTP_CREATED);
    }

    public function update(UpdateBookEditionRequest $request, BookEdition $bookEdition): JsonResponse
    {
        $data = $request->validated();
        $bookEdition->update($data);
        $bookEdition->load('book.genres', 'book.motifs');

        return response()->json([
            'message' => 'Book edition updated successfully.',
            'data' => new BookEditionResource($bookEdition),
        ]);
    }

    public function destroy(BookEdition $bookEdition): JsonResponse
    {
        $bookEdition->delete();

        return response()->json(['message' => 'Book edition deleted successfully.']);
    }
}
