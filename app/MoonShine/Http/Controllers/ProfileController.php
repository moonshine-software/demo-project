<?php

declare(strict_types=1);

namespace App\MoonShine\Http\Controllers;

use MoonShine\Laravel\Http\Controllers\MoonShineController;
use MoonShine\Support\Enums\ToastType;
use Symfony\Component\HttpFoundation\Response;

final class ProfileController extends MoonShineController
{
    public function store(): Response
    {
        return $this->json(
            __('demo.limit'),
            messageType: ToastType::WARNING
        );
    }
}
