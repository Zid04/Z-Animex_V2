<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MediaController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserFavoriteController;
use App\Http\Controllers\UserMediaController;
use App\Http\Controllers\UserRatingController;
use App\Http\Controllers\UserProgressController;

/*
|--------------------------------------------------------------------------
| API Routes (Laravel 11 + Sanctum)
|--------------------------------------------------------------------------
|
| Ces routes sont destinées à ton application Flutter.
| Elles sont totalement indépendantes de ton front Inertia.
| Elles utilisent auth:sanctum pour l’authentification par token.
|
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Media
    |--------------------------------------------------------------------------
    */
    Route::apiResource('media', MediaController::class);

    /*
    |--------------------------------------------------------------------------
    | Seasons
    |--------------------------------------------------------------------------
    */
    Route::apiResource('media.seasons', SeasonController::class)
        ->shallow();

    /*
    |--------------------------------------------------------------------------
    | Episodes
    |--------------------------------------------------------------------------
    */
    Route::apiResource('media.seasons.episodes', EpisodeController::class)
        ->shallow()
        ->except(['index', 'show']);

    /*
    |--------------------------------------------------------------------------
    | Comments
    |--------------------------------------------------------------------------
    */
    Route::prefix('media/{media}/comments')->group(function () {
        Route::get('/', [CommentController::class, 'index']);
        Route::post('/', [CommentController::class, 'store']);
        Route::patch('/{comment}', [CommentController::class, 'update']);
        Route::delete('/{comment}', [CommentController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Tags
    |--------------------------------------------------------------------------
    */
    Route::prefix('media/{media}/tags')->group(function () {
        Route::post('/', [TagController::class, 'attach']);
        Route::delete('/{tag}', [TagController::class, 'detach']);
    });

    /*
    |--------------------------------------------------------------------------
    | Favorites
    |--------------------------------------------------------------------------
    */
    Route::prefix('favorites')->group(function () {
        Route::get('/', [UserFavoriteController::class, 'index']);
        Route::post('/{media}', [UserFavoriteController::class, 'store']);
        Route::delete('/{media}', [UserFavoriteController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Watchlist
    |--------------------------------------------------------------------------
    */
    Route::prefix('watchlist')->group(function () {
        Route::get('/', [UserMediaController::class, 'index']);
        Route::post('/', [UserMediaController::class, 'store']);
        Route::patch('/{userMedia}', [UserMediaController::class, 'update']);
        Route::delete('/{userMedia}', [UserMediaController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Ratings
    |--------------------------------------------------------------------------
    */
    Route::prefix('media/{media}/ratings')->group(function () {
        Route::post('/', [UserRatingController::class, 'store']);
        Route::delete('/', [UserRatingController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Progression (Episodes vus)
    |--------------------------------------------------------------------------
    */
    Route::prefix('media/{media}/seasons/{season}/episodes/{episode}/progress')->group(function () {
        Route::post('/', [UserProgressController::class, 'store']);
        Route::delete('/', [UserProgressController::class, 'destroy']);
    });
});
