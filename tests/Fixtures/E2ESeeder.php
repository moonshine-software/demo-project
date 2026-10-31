<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Dictionary;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\Seeder;

final class E2ESeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DatabaseSeeder::class);

        $article = Article::query()->findOrFail(1);
        $article->update(['title' => 'E2E article', 'slug' => 'e2e-article', 'author_id' => 2]);
        $article->categories()->attach(Category::query()->firstOrFail());
        Category::query()->findOrFail(1)->update(['title' => 'E2E category']);
        User::query()->findOrFail(1)->update(['name' => 'E2E user']);
        Task::query()->findOrFail(1)->update(['title' => 'E2E task']);

        Comment::query()->create([
            'article_id' => $article->getKey(),
            'user_id' => User::query()->firstOrFail()->getKey(),
            'text' => 'E2E comment',
            'files' => [],
        ]);

        Dictionary::query()->create([
            'title' => 'E2E dictionary',
            'slug' => 'e2e-dictionary',
            'description' => 'Dictionary entry for browser smoke tests.',
        ]);
    }
}
