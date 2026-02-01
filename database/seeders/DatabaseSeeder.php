<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Models\MoonshineUserRole;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        MoonshineUser::query()->create([
            'name' => 'Admin',
            'moonshine_user_role_id' => MoonshineUserRole::DEFAULT_ROLE_ID,
            'email' => 'admin@moonshine-laravel.com',
            'password' => bcrypt('moonshine')
        ]);

        MoonshineUserRole::query()->create([
            'id' => 2,
            'name' => 'Author'
        ]);

        MoonshineUser::query()->create([
            'name' => 'Author',
            'moonshine_user_role_id' => 2,
            'email' => 'author@moonshine-laravel.com',
            'password' => bcrypt('moonshine')
        ]);

        Article::factory(20)->create();
        Category::factory(10)->create();
        User::factory(10)->create();

        $statuses = [
            ['name' => 'To Do', 'sorting' => 1],
            ['name' => 'In Progress', 'sorting' => 2],
            ['name' => 'Review', 'sorting' => 3],
            ['name' => 'Done', 'sorting' => 4],
        ];

        foreach ($statuses as $status) {
            Status::create($status);
        }

        Task::factory(50)->create();

        DB::table('settings')->insert([
            'id' => 1,
            'email' => fake()->email(),
            'phone' => fake()->e164PhoneNumber(),
            'copyright' => now()->year
        ]);
    }
}
