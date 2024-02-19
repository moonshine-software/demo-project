<?php

use Illuminate\Support\Facades\Route;
use App\MoonShine\Controllers\ProfileController;
use UniSharp\LaravelFilemanager\Lfm;

if (config('moonshine.tinymce.file_manager')) {
    Route::prefix('laravel-filemanager')->group(function () {
        Lfm::routes();
    });
}

if (config('app.demo_mode', false)) {
    Route::post('/admin/profile', [ProfileController::class, 'store'])
        ->middleware(config('moonshine.auth.middleware', []))
        ->name('profile.store');
}
