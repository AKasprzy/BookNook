<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as Status;

class ReviewController extends Controller
{
    public function index(): JsonResponse
    {
        $reviews = Review::with(['book', 'user'])->paginate(request('per_page', 10));

        return ReviewResource::collection($reviews)->response();
    }

    public function show(Review $review): JsonResponse
    {
        $review->load(['book', 'user']);

        return ReviewResource::make($review)->response();
    }

    public function store(StoreReviewRequest $request): JsonResponse
    {
        $review = Review::create([
            'user_id' => $request->user()->id,
            ...$request->validated(),
        ]);

        return ReviewResource::make($review->load(['book', 'user']))
            ->additional(['message' => 'Review created successfully.'])
            ->response()
            ->setStatusCode(Status::HTTP_CREATED);
    }

    public function update(UpdateReviewRequest $request, Review $review): JsonResponse
    {
        $review->update($request->validated());

        return ReviewResource::make($review->load(['book', 'user']))
            ->additional(['message' => 'Review updated successfully.'])
            ->response();
    }

    public function destroy(Review $review): JsonResponse
    {
        $review->delete();

        return response()->json(['message' => 'Review deleted successfully.']);
    }
}
