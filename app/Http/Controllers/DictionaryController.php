<?php

namespace App\Http\Controllers;

use App\Models\Dictionary;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View;

class DictionaryController extends Controller
{
    public function index(): View\Factory|View\View|Application
    {
        return view('dictionaries.index', [
            'dictionaries' => Dictionary::query()->paginate(10)
        ]);
    }

    public function show(Dictionary $dictionary): View\Factory|View\View|Application
    {
        return view('dictionaries.show', compact('dictionary'));
    }
}
