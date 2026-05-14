<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Shelve;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function show(Request $request, User $user)
    {
        $recentlyReadBooks = Shelve::query()
            ->where('user_id', $user->id)
            ->where('status', 'read')
            ->with([
                'bookEdition.book:id,title,author',
            ])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->map(function ($shelf) {
                return [
                    'id' => $shelf->id,
                    'status' => $shelf->status,
                    'times_read' => $shelf->times_read,
                    'favourite' => (bool) $shelf->favourite,
                    'read_at' => $shelf->created_at,
                    'edition' => [
                        'id' => $shelf->bookEdition->id,
                        'book_id' => $shelf->bookEdition->book->id,
                        'edition_title' => $shelf->bookEdition->edition_title,
                        'format' => $shelf->bookEdition->format,
                        'description' => $shelf->bookEdition->description,
                        'cover_url' => $shelf->bookEdition->cover_url,
                        'book' => [
                            'id' => $shelf->bookEdition->book->id,
                            'title' => $shelf->bookEdition->book->title,
                            'author' => $shelf->bookEdition->book->author,
                        ],
                    ],
                ];
            });

        $userReviews = Review::query()
            ->where('user_id', $user->id)
            ->with(['bookEdition.book:id,title,author'])
            ->select([
                'id',
                'book_edition_id',
                'rating',
                'review_text',
                'spoiler',
                'reread',
                'reviewed_at',
            ])
            ->orderByDesc('reviewed_at')
            ->limit(10)
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'book_id' => $review->bookEdition->book->id,
                    'rating' => $review->rating,
                    'review_text' => $review->review_text,
                    'spoiler' => (bool) $review->spoiler,
                    'reread' => (bool) $review->reread,
                    'reviewed_at' => $review->reviewed_at,
                    'book' => [
                        'id' => $review->bookEdition->book->id,
                        'title' => $review->bookEdition->book->title,
                        'author' => $review->bookEdition->book->author,
                    ],
                ];
            });

        return Inertia::render('UserProfilePage', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'location' => $user->location ?? null,
                'joined_at' => optional($user->created_at)->toDateString(),
                'bio' => $user->bio ?? null,
                'favourite_genres' => $user->favourite_genres ?? [],
                'role' => $user->getRoleNames()->first(),
            ],
            'recentlyReadBooks' => $recentlyReadBooks,
            'userReviews' => $userReviews,
            'backUrl' => url()->previous(),
            'statsUrl' => null,
        ]);
    }

    public function getCurrentUser()
    {
        $user = Auth::user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->getRoleNames()->first(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
        ]);

        $user->update($validated);

        return response()->json(['message' => 'Profile updated']);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return response()->json(['message' => 'Invalid password'], 422);
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return response()->json(['message' => 'Password updated']);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'password' => ['required'],
        ]);

        if (! Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid password'], 422);
        }

        $user->delete();

        return response()->json(['message' => 'Account deleted']);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'deleted']);
    }

    public function count()
    {
        return response()->json(['total_users' => User::count()]);
    }
}
