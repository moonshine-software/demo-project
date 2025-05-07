<?php

use App\MoonShine\Http\Controllers\ProfileController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

if (config('app.demo_mode', false)) {
    Route::moonshine(static function (Router $router) {
        $router->post('/profile', [ProfileController::class, 'store'])
            ->name('profile.store');
    });
}
