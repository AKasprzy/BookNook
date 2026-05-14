<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookEditionRequest;
use App\Http\Requests\UpdateBookEditionRequest;
use App\Http\Resources\BookEditionResource;
use App\Models\Book;
use App\Models\BookEdition;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
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

    public function store(Book $book, StoreBookEditionRequest $request): JsonResponse
    {
        $data = $request->validated();
        unset($data['book_id']);

        $bookEdition = $book->editions()->create($data);

        return response()->json([
            'message' => 'Book edition created successfully.',
            'data' => new BookEditionResource($bookEdition),
        ], Status::HTTP_CREATED);
    }

    public function update(UpdateBookEditionRequest $request, BookEdition $bookEdition): JsonResponse
    {
        $this->authorize('update', $bookEdition);

        $data = $request->validated();
        $bookEdition->update($data);

        return response()->json([
            'message' => 'Book edition updated successfully.',
            'data' => new BookEditionResource($bookEdition),
        ]);
    }

    public function destroy(BookEdition $bookEdition): JsonResponse
    {
        $this->authorize('delete', $bookEdition);

        $book = $bookEdition->book;

        if ($book->editions()->count() <= 1) {
            return response()->json([
                'message' => 'A book must have at least one edition.',
            ], Status::HTTP_UNPROCESSABLE_ENTITY);
        }

        $bookEdition->delete();

        return response()->json([
            'message' => 'Book edition deleted successfully.',
        ]);
    }

    public function count()
    {
        return response()->json([
            'total_editions' => BookEdition::count(),
        ]);
    }

    public function byFormat()
    {
        $data = BookEdition::select('format', DB::raw('count(*) as total'))
            ->groupBy('format')
            ->get();

        return response()->json([
            'data' => $data,
        ]);
    }
}
