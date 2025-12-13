<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();

            // TMDB unique identifier for the movie
            $table->integer('tmdb_id')->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');

            // Details about the movie
            $table->date('release_date')->nullable();
            $table->smallInteger('runtime')->nullable();
            $table->string('language', 10)->nullable();
            $table->string('poster_url')->nullable();
            $table->string('trailer_link')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
