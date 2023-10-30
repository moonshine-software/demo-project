<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MoonShine\ChangeLog\Traits\HasChangeLog;

class Dictionary extends Model
{
    use HasFactory;
    use HasChangeLog;

    protected $fillable = [
        'title',
        'description',
        'slug'
    ];
}
