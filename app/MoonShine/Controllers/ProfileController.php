<?php

declare(strict_types=1);

namespace App\MoonShine\Controllers;

use MoonShine\Enums\ToastType;
use MoonShine\MoonShineRequest;
use MoonShine\Http\Controllers\MoonShineController;
use Symfony\Component\HttpFoundation\Response;

final class ProfileController extends MoonShineController
{
    public function store(MoonShineRequest $request): Response
    {
        return $this->json(__('demo.limit'), messageType: ToastType::WARNING);
    }
}
