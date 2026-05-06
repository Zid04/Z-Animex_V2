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
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')
                  ->constrained('seasons')
                  ->cascadeOnDelete();            
            $table->unsignedInteger('number');
            $table->string('title')->nullable();
            $table->integer('duration')->nullable()->comment('Durée en minutes');
            $table->string('video_url')->nullable()->comment('Lien du streaming');

            $table->timestamps();
            //Empêche doublons 
            $table->unique(['season_id', 'number']);
            $table->index('season_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};