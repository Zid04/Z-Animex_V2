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
        Schema::create('episode_user', function (Blueprint $table) {
            $table->id();
            // Relations
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('episode_id')
                  ->constrained('episodes')
                  ->cascadeOnDelete();
           $table->timestamp('watched_at')->nullable();
           $table->integer('progress_seconds')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'episode_id']);
            $table->index('user_id');
            $table->index('episode_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episode_user');
    }
};