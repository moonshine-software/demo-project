<?php

declare(strict_types=1);

namespace App\MoonShine\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

final class UserFetchController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json(
            User::all()
        );
    }
}
