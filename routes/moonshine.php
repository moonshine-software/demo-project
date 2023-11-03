<?php

use Illuminate\Support\Facades\Route;
use UniSharp\LaravelFilemanager\Lfm;

if(config('moonshine.tinymce.file_manager')) {
    Route::prefix('laravel-filemanager')->group(function () {
        Lfm::routes();
    });
}
