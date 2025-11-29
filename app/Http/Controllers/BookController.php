<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as Status;

class BookController extends Controller
{
    public function index(): JsonResponse
    {
        $perPage = request()->integer('per_page', 10);
        $paginated = Book::paginate($perPage);

        return BookResource::collection($paginated)->response();
    }

    public function show(Book $book): JsonResponse
    {
        $book->load('editions');

        return response()->json([
            'data' => new BookResource($book),
        ]);
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        $data = $request->validated();
        $book = Book::create($data);

        return response()->json([
            'message' => 'Book created successfully.',
            'data' => new BookResource($book),
        ], Status::HTTP_CREATED);
    }

    public function update(UpdateBookRequest $request, Book $book): JsonResponse
    {
        $data = $request->validated();
        $book->update($data);
        $book->load('editions');

        return response()->json([
            'message' => 'Book updated successfully.',
            'data' => new BookResource($book),
        ]);
    }

    public function destroy(Book $book): JsonResponse
    {
        $book->delete();

        return response()->json([
            'message' => 'Book deleted successfully.',
        ]);
    }
}
