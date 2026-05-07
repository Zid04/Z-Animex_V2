<?php

use App\Http\Controllers\Web\MediaWebController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/media',              [MediaWebController::class, 'index'])->name('media.index');
    Route::get('/media/create',       [MediaWebController::class, 'create'])->name('media.create');
    Route::post('/media',             [MediaWebController::class, 'store'])->name('media.store');
    Route::get('/my-media',           [MediaWebController::class, 'mine'])->name('media.mine');
    Route::get('/media/{media}',      [MediaWebController::class, 'show'])->name('media.show')->where('media', '[0-9]+');
    Route::get('/media/{media}/edit', [MediaWebController::class, 'edit'])->name('media.edit')->where('media', '[0-9]+');
    Route::patch('/media/{media}',    [MediaWebController::class, 'update'])->name('media.update')->where('media', '[0-9]+');
    Route::delete('/media/{media}',   [MediaWebController::class, 'destroy'])->name('media.destroy')->where('media', '[0-9]+');

});