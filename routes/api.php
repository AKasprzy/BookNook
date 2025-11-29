<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookEditionController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\MotifController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShelveController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [LogoutController::class, 'logout']);
});

Route::get('books', [BookController::class, 'index']);
Route::get('books/{book}', [BookController::class, 'show']);

Route::get('book-editions', [BookEditionController::class, 'index']);
Route::get('book-editions/{bookEdition}', [BookEditionController::class, 'show']);

Route::get('genres', [GenreController::class, 'index']);
Route::get('genres/{genre}', [GenreController::class, 'show']);

Route::get('motifs', [MotifController::class, 'index']);
Route::get('motifs/{motif}', [MotifController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('books', [BookController::class, 'store']);

    Route::post('book-editions', [BookEditionController::class, 'store']);

    Route::get('reviews', [ReviewController::class, 'index']);
    Route::get('reviews/{review}', [ReviewController::class, 'show']);
    Route::post('reviews', [ReviewController::class, 'store']);
    Route::put('reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy']);

    Route::get('shelves', [ShelveController::class, 'index']);
    Route::get('shelves/{shelve}', [ShelveController::class, 'show']);
    Route::post('shelves', [ShelveController::class, 'store']);
    Route::put('shelves/{shelve}', [ShelveController::class, 'update']);
    Route::delete('shelves/{shelve}', [ShelveController::class, 'destroy']);

    Route::middleware('role:moderator|admin|superAdmin')->group(function () {
        Route::post('genres', [GenreController::class, 'store']);
        Route::put('genres/{genre}', [GenreController::class, 'update']);
        Route::delete('genres/{genre}', [GenreController::class, 'destroy']);

        Route::post('motifs', [MotifController::class, 'store']);
        Route::put('motifs/{motif}', [MotifController::class, 'update']);
        Route::delete('motifs/{motif}', [MotifController::class, 'destroy']);

        Route::put('books/{book}', [BookController::class, 'update']);
        Route::delete('books/{book}', [BookController::class, 'destroy']);

        Route::put('book-editions/{bookEdition}', [BookEditionController::class, 'update']);
        Route::delete('book-editions/{bookEdition}', [BookEditionController::class, 'destroy']);
    });
});
