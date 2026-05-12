<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserProgressController;
use App\Http\Controllers\UserFavoriteController;
use App\Http\Controllers\UserMediaController;
use App\Http\Controllers\UserRatingController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::get('/avatars/defaults/{filename}', function ($filename) {
    if (! preg_match('/^avatar-\d+\.(jpg|png|webp|avif)$/i', $filename)) {
        abort(404);
    }

    $path = public_path("avatars/defaults/{$filename}");

    if (! file_exists($path)) {
        abort(404);
    }

    $mime = match (true) {
        str_ends_with($filename, '.png')  => 'image/png',
        str_ends_with($filename, '.webp') => 'image/webp',
        str_ends_with($filename, '.avif') => 'image/avif',
        default                           => 'image/jpeg',
    };

    return response()->file($path, [
        'Content-Type'  => $mime,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->name('avatars.default');

/*
|--------------------------------------------------------------------------
| Authenticated
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    | Favoris
    */
    Route::prefix('favorites')->name('favorites.')->group(function () {
        Route::get('/', [UserFavoriteController::class, 'index'])->name('index');
        Route::post('/{media}', [UserFavoriteController::class, 'store'])->name('store')->where('media', '[0-9]+');
        Route::delete('/{media}', [UserFavoriteController::class, 'destroy'])->name('destroy')->where('media', '[0-9]+');
    });

    /*
    | Progression
    */
    Route::prefix('media/{media}/seasons/{season}/episodes/{episode}/progress')
        ->name('progress.')
        ->group(function () {
            Route::post('/', [UserProgressController::class, 'store'])->name('store');
            Route::delete('/', [UserProgressController::class, 'destroy'])->name('destroy');
        });

    /*
    | Watchlist (user-media)
    */
    Route::prefix('watchlist')->name('watchlist.')->group(function () {
        Route::get('/', [UserMediaController::class, 'index'])->name('index');
        Route::post('/', [UserMediaController::class, 'store'])->name('store');
        Route::patch('/{userMedia}', [UserMediaController::class, 'update'])->name('update');
        Route::delete('/{userMedia}', [UserMediaController::class, 'destroy'])->name('destroy');
    });

    /*
    | Notes
    */
    Route::prefix('media/{media}/ratings')
        ->name('ratings.')
        ->group(function () {
            Route::post('/', [UserRatingController::class, 'store'])->name('store')->where('media', '[0-9]+');
            Route::delete('/', [UserRatingController::class, 'destroy'])->name('destroy')->where('media', '[0-9]+');
        });

    /*
    |--------------------------------------------------------------------------
    | API (session web)
    |--------------------------------------------------------------------------
    */
    Route::prefix('api')->name('api.')->group(function () {

        Route::apiResource('media', MediaController::class)
        ->parameters(['media' => 'media']);
Route::apiResource('media.seasons', SeasonController::class)
    ->shallow()
    ->parameters(['media' => 'media']);

        Route::apiResource('media.seasons.episodes', EpisodeController::class)
            ->shallow()
            ->except(['index', 'show'])
                ->parameters(['media' => 'media', 'seasons' => 'season', 'episodes' => 'episode']);

       Route::prefix('media/{media}/comment')->name('comment.')->group(function () {
    Route::get('/', [CommentController::class, 'index'])->name('index');
    Route::post('/', [CommentController::class, 'store'])->name('store');
    Route::patch('/{comment}', [CommentController::class, 'update'])->name('update');
    Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('destroy');


        });

        Route::prefix('media/{media}/tags')->name('tags.')->group(function () {
            Route::post('/', [TagController::class, 'attach'])->name('attach');
            Route::delete('/{tag}', [TagController::class, 'detach'])->name('detach');
        });
    });
});

require __DIR__ . '/settings.php';
require __DIR__ . '/media.php';