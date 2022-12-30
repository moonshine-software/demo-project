<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\Traits\Models\HasMoonShineChangeLog;
use Leeto\Thumbnails\Traits\WithThumbnails;

class Article extends Model
{
    use HasFactory;
    use WithThumbnails;
    use HasMoonShineChangeLog;

    protected $fillable = [
        'title',
        'description',
        'thumbnail',
        'slug',
        'seo_title',
        'seo_description',
        'author_id'
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(MoonshineUser::class);
    }
}
