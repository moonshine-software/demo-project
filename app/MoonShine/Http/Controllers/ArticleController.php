<?php

declare(strict_types=1);

namespace App\MoonShine\Http\Controllers;

use App\MoonShine\Actions\ArticleMassActive;
use App\MoonShine\Http\Requests\ArticleMassActiveFormRequest;
use MoonShine\Laravel\Http\Controllers\MoonShineController;
use MoonShine\Support\Enums\ToastType;
use Symfony\Component\HttpFoundation\Response;

final class ArticleController extends MoonshineController
{
    public function massActive(
        ArticleMassActiveFormRequest $request,
        ArticleMassActive $action,
    ): Response {
        $ids = $request
            ->collect('ids')
            ->filter()
            ->toArray();

        $success = $action($ids);
        $message = __('moonshine::ui.saved');
        $type = ToastType::SUCCESS;

        if (! $success) {
            $message = __('moonshine::ui.saved_error');
            $type = ToastType::ERROR;
        }

        if ($request->ajax()) {
            return $this->json($message, messageType: $type);
        }

        $this->toast($message, $type);

        return back();
    }
}
