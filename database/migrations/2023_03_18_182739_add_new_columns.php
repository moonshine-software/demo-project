<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', static function (Blueprint $table) {
            $table->integer('rating')->default(0);
            $table->integer('age_from')->default(0);
            $table->integer('age_to')->default(60);
            $table->boolean('active')->default(true);
            $table->string('link')->nullable();
            $table->string('color')->nullable();
            $table->text('files')->nullable();
            $table->text('code')->nullable();
            $table->json('data')->nullable();
        });

        Schema::table('categories', static function (Blueprint $table) {
            $table->foreignIdFor(Category::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });

        Schema::table('users', static function (Blueprint $table) {
            $table->integer('phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
