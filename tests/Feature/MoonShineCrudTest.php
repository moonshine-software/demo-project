<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Dictionary;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use MoonShine\Laravel\Models\MoonshineUserRole;
use MoonShine\Permissions\Models\MoonshineUser;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\E2ESeeder;
use Tests\TestCase;

final class MoonShineCrudTest extends TestCase
{
    // Fixtures use fixed IDs; MySQL does not roll back auto-increment counters.
    use DatabaseMigrations;

    protected function beforeRefreshingDatabase(): void
    {
        $connection = config('database.default');
        self::assertSame('testing', $this->app->environment());
        self::assertContains(config("database.connections.$connection.database"), [':memory:', 'moonshine_demo_test']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.demo_mode' => false]);
        $this->seed(E2ESeeder::class);
        $this->actingAs(MoonshineUser::query()->findOrFail(1), 'moonshine');
    }

    public static function resources(): iterable
    {
        yield 'articles' => ['article-resource', Article::class, 'title', [
            'title' => 'CRUD article', 'slug' => 'crud-article', 'author_id' => 1,
            'description' => 'Article created by the integration test.',
        ]];
        yield 'categories' => ['category-resource', Category::class, 'title', ['title' => 'CRUD category']];
        yield 'comments' => ['comment-resource', Comment::class, 'text', [
            'article_id' => 1, 'user_id' => 1, 'text' => 'CRUD comment',
        ]];
        yield 'dictionaries' => ['dictionary-resource', Dictionary::class, 'title', [
            'title' => 'CRUD dictionary', 'slug' => 'crud-dictionary', 'description' => 'Dictionary created by the test.',
        ]];
        yield 'users' => ['user-resource', User::class, 'name', [
            'name' => 'CRUD user', 'email' => 'crud-user@example.com',
            'password' => 'Password123!', 'password_repeat' => 'Password123!',
        ]];
        yield 'admins' => ['moon-shine-user-resource', MoonshineUser::class, 'name', [
            'name' => 'CRUD admin', 'email' => 'crud-admin@example.com', 'moonshine_user_role_id' => 1,
            'password' => 'Password123!', 'password_confirmation' => 'Password123!',
        ]];
        yield 'roles' => ['moon-shine-user-role-resource', MoonshineUserRole::class, 'name', ['name' => 'CRUD role']];
        yield 'tasks' => ['task-resource', Task::class, 'title', [
            'title' => 'CRUD task', 'status_id' => 1, 'user_id' => 1, 'description' => 'Task created by the test.',
        ]];
    }

    #[DataProvider('resources')]
    public function test_resource_crud(string $resource, string $model, string $column, array $data): void
    {
        $url = route('moonshine.crud.store', ['resourceUri' => $resource]);
        $this->postJson($url, $data)->assertCreated();

        $item = $model::query()->where($column, $data[$column])->sole();
        $itemUrl = $url.'/'.$item->getKey();
        $this->getJson($url)->assertOk();

        $data[$column] .= ' updated';
        $this->putJson($itemUrl, $data)->assertOk();
        $this->assertSame($data[$column], $item->fresh()->getAttribute($column));

        $this->deleteJson($itemUrl)->assertOk();
        $this->assertModelMissing($item);
    }

    public function test_invalid_article_is_rejected_without_saving(): void
    {
        $count = Article::query()->count();

        $this->postJson(route('moonshine.crud.store', ['resourceUri' => 'article-resource']), [
            'title' => '', 'slug' => '', 'description' => '',
        ])->assertUnprocessable()->assertJsonValidationErrors(['title', 'slug', 'description']);

        $this->assertSame($count, Article::query()->count());
    }

    public function test_author_cannot_manage_admins_or_delete_articles(): void
    {
        $this->actingAs(MoonshineUser::query()->findOrFail(2), 'moonshine');

        $this->getJson(route('moonshine.crud.index', [
            'resourceUri' => 'moon-shine-user-resource',
        ]))->assertForbidden();
        $this->deleteJson(route('moonshine.crud.destroy', [
            'resourceUri' => 'article-resource', 'resourceItem' => 1,
        ]))->assertForbidden();

        $this->assertModelExists(Article::query()->findOrFail(1));
    }

    public function test_settings_can_be_saved(): void
    {
        $this->putJson(route('moonshine.crud.update', [
            'resourceUri' => 'setting-resource', 'resourceItem' => 1,
        ]), [
            'email' => 'settings@example.com', 'phone' => '+1234567890', 'copyright' => '2030',
        ])->assertOk();

        $this->assertDatabaseHas('settings', ['id' => 1, 'email' => 'settings@example.com', 'copyright' => '2030']);
    }

    public function test_demo_mode_protects_admin_accounts(): void
    {
        config(['app.demo_mode' => true]);
        $this->get('/')->assertRedirect('/admin');
        $this->putJson(route('moonshine.crud.update', [
            'resourceUri' => 'moon-shine-user-resource', 'resourceItem' => 1,
        ]), ['name' => 'Changed admin'])->assertForbidden();
        $this->assertDatabaseHas('moonshine_users', ['id' => 1, 'name' => 'Admin']);
    }
}
