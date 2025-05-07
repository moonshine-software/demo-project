<?php

declare(strict_types=1);

namespace App\MoonShine\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

final class ArticleMassActiveFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'confirm' => 'accepted',
            'ids' => ['required', 'array'],
        ];
    }
}
