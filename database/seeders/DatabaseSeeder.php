<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('moonshine_user_roles')->insert([
            'id' => 2,
            'name' => 'Author'
        ]);

        Article::factory(100)->create();
    }
}
