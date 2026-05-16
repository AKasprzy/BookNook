<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Models\Genre;
use App\Models\Motif;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/login', fn () => Inertia::render('Auth/Login'))->name('login');
Route::get('/register', fn () => Inertia::render('Auth/Register'))->name('register');

Route::post('/register', [RegisterController::class, 'registerWeb']);

Route::get('/', function () {
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/book-editions/edit/{edition}', function ($edition) {
    return Inertia::render('BookEditions/Edit', [
        'availableGenres' => Genre::all(),
        'availableMotifs' => Motif::all(),
    ]);
})->name('book-editions.edit');

Route::get('/books/{book}/editions', function ($book) {
    return Inertia::render('Books/Editions', [
        'bookId' => (int) $book,
    ]);
});

Route::get('/reviews/create/{bookEdition}', function ($bookEdition) {
    return Inertia::render('Reviews/Create', [
        'bookEditionId' => (int) $bookEdition,
    ]);
})->name('reviews.create');

Route::get('/reviews/edit/{review}', function ($review) {
    return Inertia::render('Reviews/Update', [
        'reviewId' => (int) $review,
    ]);
})->name('reviews.edit');

Route::get('/book-editions/create/{book}', function ($book) {
    return Inertia::render('BookEditions/Create', [
        'bookId' => (int) $book,
    ]);
})->name('book-editions.create');

Route::get('/search', fn () => Inertia::render('Search'))->name('search');
Route::get('/shelves', fn () => Inertia::render('Shelves'))->name('shelves');

Route::get('/books/create', function () {
    return Inertia::render('Books/Create', [
        'availableGenres' => Genre::all(),
        'availableMotifs' => Motif::all(),
    ]);
})->name('books.create');

Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

Route::get('/home', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/books/{book}', function ($book) {
    return Inertia::render('Books/Show', [
        'bookId' => (int) $book,
        'editionId' => null,
    ]);
});

Route::get('/books/{book}/editions/{edition}', function ($book, $edition) {
    return Inertia::render('Books/Show', [
        'bookId' => (int) $book,
        'editionId' => (int) $edition,
    ]);
});

Route::get('/settings', function () {
    return Inertia::render('UserSettings');
})->name('settings');

Route::get('/stats', function () {
    return Inertia::render('Stats');
})->name('stats');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
