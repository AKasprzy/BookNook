<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookEditionController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\MotifController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShelveController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserStatsController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [LogoutController::class, 'logout']);
});

Route::get('books/latest', [BookController::class, 'latest']);
Route::get('books/per-year', [BookController::class, 'booksPerYear']);
Route::get('book-editions/by-format', [BookEditionController::class, 'byFormat']);

Route::get('books', [BookController::class, 'index']);
Route::get('books/{book}', [BookController::class, 'show']);

Route::get('book-editions', [BookEditionController::class, 'index']);
Route::get('/book-editions/count', [BookEditionController::class, 'count']);
Route::get('book-editions/{bookEdition}', [BookEditionController::class, 'show']);

Route::get('genres', [GenreController::class, 'index']);
Route::get('genres/{genre}', [GenreController::class, 'show']);

Route::get('motifs', [MotifController::class, 'index']);
Route::get('motifs/{motif}', [MotifController::class, 'show']);

Route::get('/users/count', [UserController::class, 'count']);
Route::get('/reviews/count', [ReviewController::class, 'count']);
Route::get('reviews/latest', [ReviewController::class, 'latest']);

Route::get('reviews', [ReviewController::class, 'index']);
Route::get('reviews/by-edition', [ReviewController::class, 'byEdition']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('genres', [GenreController::class, 'store']);
    Route::put('genres/{genre}', [GenreController::class, 'update']);
    Route::delete('genres/{genre}', [GenreController::class, 'destroy']);

    Route::post('motifs', [MotifController::class, 'store']);
    Route::put('motifs/{motif}', [MotifController::class, 'update']);
    Route::delete('motifs/{motif}', [MotifController::class, 'destroy']);

    Route::get('/user', [UserController::class, 'getCurrentUser']);
    Route::put('/user/settings/profile', [UserController::class, 'updateProfile']);
    Route::put('/user/settings/password', [UserController::class, 'updatePassword']);
    Route::delete('/user/delete', [UserController::class, 'deleteAccount']);

    Route::get('/user/stats', [UserStatsController::class, 'index']);

    Route::get('/search', [SearchController::class, 'search']);

    Route::get('my-shelves', [ShelveController::class, 'myEditions']);
    Route::get('my-shelves/status/{status}', [ShelveController::class, 'myEditionsByStatus']);

    Route::get('my-reviews', [ReviewController::class, 'myReviews']);

    Route::post('books', [BookController::class, 'store']);
    Route::put('books/{book}', [BookController::class, 'update']);
    Route::delete('books/{book}', [BookController::class, 'destroy']);
    Route::delete('books/{book}/force', [BookController::class, 'forceDestroy']);

    Route::post('books/{book}/editions', [BookEditionController::class, 'store']);

    Route::get('reviews/{review}', [ReviewController::class, 'show']);
    Route::post('reviews', [ReviewController::class, 'store']);
    Route::put('reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy']);
    Route::delete('reviews/{review}/force', [ReviewController::class, 'forceDestroy']);

    Route::get('shelves', [ShelveController::class, 'index']);
    Route::get('shelves/{shelve}', [ShelveController::class, 'show']);
    Route::post('shelves', [ShelveController::class, 'store']);
    Route::put('shelves/{shelve}', [ShelveController::class, 'update']);
    Route::delete('shelves/{shelve}', [ShelveController::class, 'destroy']);

    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    Route::put('book-editions/{bookEdition}', [BookEditionController::class, 'update']);
    Route::delete('book-editions/{bookEdition}', [BookEditionController::class, 'destroy']);
    Route::delete('book-editions/{bookEdition}/force', [BookEditionController::class, 'forceDestroy']);
});
