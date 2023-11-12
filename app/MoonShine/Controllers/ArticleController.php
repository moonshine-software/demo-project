<?php

declare(strict_types=1);

namespace App\MoonShine\Controllers;

use App\Models\Article;
use MoonShine\MoonShineRequest;
use MoonShine\Http\Controllers\MoonshineController;
use Symfony\Component\HttpFoundation\Response;

final class ArticleController extends MoonshineController
{
    public function massActive(MoonShineRequest $request): Response
    {
        $request->validate([
            'confirm' => 'accepted'
        ]);

        $ids = $request->collect('ids')
            ->filter()
            ->toArray();

        Article::query()
            ->whereIn('id', $ids)
            ->update(['active' => true]);

        if($request->ajax()) {
            return response()->json([
                'message' => __('moonshine::ui.saved')
            ]);
        }

        $this->toast(__('moonshine::ui.saved'));

        return back();
    }
}
