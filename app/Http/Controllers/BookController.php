<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
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
        return DB::transaction(function () use ($request) {

            $data = $request->validated();

            $book = Book::create([
                'title' => $data['title'],
                'original_language' => $data['original_language'],
                'author' => $data['author'],
                'original_publication_date' => $data['original_publication_date'] ?? null,
                'series' => $data['series'] ?? null,
            ]);

            if (! empty($data['genre_ids'])) {
                $book->genres()->sync($data['genre_ids']);
            }

            if (! empty($data['motif_ids'])) {
                $book->motifs()->sync($data['motif_ids']);
            }

            $book->editions()->create($data['edition']);

            $book->load('editions', 'genres', 'motifs');

            return response()->json([
                'message' => 'Book and first edition created successfully.',
                'data' => new BookResource($book),
            ], Status::HTTP_CREATED);
        });
    }

    public function update(UpdateBookRequest $request, Book $book): JsonResponse
    {
        $this->authorize('update', $book);

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
        $this->authorize('delete', $book);

        $book->delete();

        return response()->json([
            'message' => 'Book deleted successfully.',
        ]);
    }
}
