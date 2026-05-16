<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as Status;

class ReviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Review::with(['bookEdition.book', 'user']);

        if ($request->filled('book_edition_id')) {
            $query->where('book_edition_id', $request->book_edition_id);
        }

        return ReviewResource::collection(
            $query->paginate($request->get('per_page', 10))
        )->response();
    }

    public function byEdition(Request $request): JsonResponse
    {
        $reviews = Review::with(['bookEdition.book', 'user'])
            ->where('book_edition_id', $request->book_edition_id)
            ->latest()
            ->get();

        return ReviewResource::collection($reviews)->response();
    }

    public function myReviews(Request $request): JsonResponse
    {
        $reviews = Review::with(['bookEdition.book'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return ReviewResource::collection($reviews)->response();
    }

    public function show(Review $review): JsonResponse
    {
        $review->load(['bookEdition.book', 'user']);

        return ReviewResource::make($review)->response();
    }

    public function store(StoreReviewRequest $request): JsonResponse
    {
        $review = Review::create([
            'user_id' => $request->user()->id,
            ...$request->validated(),
        ]);

        return ReviewResource::make($review->load(['bookEdition.book', 'user']))
            ->additional(['message' => 'Review created successfully.'])
            ->response()
            ->setStatusCode(Status::HTTP_CREATED);
    }

    public function update(UpdateReviewRequest $request, Review $review): JsonResponse
    {
        $this->authorize('update', $review);

        $review->update($request->validated());

        return ReviewResource::make($review->load(['bookEdition.book', 'user']))
            ->additional(['message' => 'Review updated successfully.'])
            ->response();
    }

    public function destroy(Review $review): JsonResponse
    {
        $this->authorize('delete', $review);

        $review->delete();

        return response()->json(['message' => 'Review hidden successfully.']);
    }

    public function forceDestroy(Review $review): JsonResponse
    {
        $this->authorize('delete', $review);

        $review->forceDelete();

        return response()->json(['message' => 'Review permanently deleted.']);
    }

    public function latest(): JsonResponse
    {
        $reviews = Review::with(['bookEdition.book', 'user'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        return ReviewResource::collection($reviews)->response();
    }

    public function count()
    {
        return response()->json(['total_reviews' => Review::count()]);
    }
}
