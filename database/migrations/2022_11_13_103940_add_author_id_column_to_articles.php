<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('author_id')
                ->nullable()
                ->constrained('moonshine_users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }
};
