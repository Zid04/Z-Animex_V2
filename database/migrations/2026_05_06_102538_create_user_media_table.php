<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_media', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('media_id')
                  ->constrained('media')
                  ->cascadeOnDelete();

            $table->enum('status', ['watching', 'completed', 'planned', 'dropped'])
                  ->default('planned')
                  ->index();

            // ✔ UNE SEULE COLONNE progress
            $table->unsignedInteger('progress')->default(0)->comment('Episodes vus');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // Index
            $table->unique(['user_id', 'media_id']);
            $table->index(['user_id', 'status']);
            $table->index('media_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_media');
    }
};