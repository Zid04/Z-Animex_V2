<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->unsignedBigInteger('external_id')->nullable()->index();
            $table->string('source')->nullable();
            $table->enum('type', ['anime', 'movie', 'series'])->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('cover')->nullable();
            $table->json('images')->nullable();
            $table->string('status')->nullable();
            $table->boolean('airing')->default(false)->index();

            $table->boolean('is_public')->default(true);

            $table->boolean('approved')->default(false)->index();

            $table->unsignedInteger('episodes')->nullable();
            $table->string('duration')->nullable();
            $table->year('year')->nullable()->index();

            $table->float('score')->nullable()->index();
            $table->unsignedInteger('scored_by')->nullable();
            $table->unsignedInteger('rank')->nullable()->index();
            $table->unsignedInteger('popularity')->nullable()->index();
            $table->unsignedInteger('members')->nullable();
            $table->unsignedInteger('favorites')->nullable();

            $table->json('studios')->nullable();
            $table->json('genres')->nullable();

            $table->timestamps();

            
            $table->index(['type', 'approved']);
            $table->index(['airing', 'score']);
            $table->index(['user_id', 'is_public']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
