<?php

namespace Tests\Feature\Web;

use App\Models\Episode;
use App\Models\Media;
use App\Models\Season;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressTest extends TestCase
{
    use RefreshDatabase;

    private function progressStoreRoute(Media $media, Season $season, Episode $episode): string
    {
        return route('progress.store', [
            'media'   => $media->id,
            'season'  => $season->id,
            'episode' => $episode->id,
        ]);
    }

    private function progressDestroyRoute(Media $media, Season $season, Episode $episode): string
    {
        return route('progress.destroy', [
            'media'   => $media->id,
            'season'  => $season->id,
            'episode' => $episode->id,
        ]);
    }

    public function test_guest_cannot_mark_episode_as_watched(): void
    {
        $media   = Media::factory()->create(['is_public' => true, 'approved' => true]);
        $season  = Season::factory()->create(['media_id' => $media->id]);
        $episode = Episode::factory()->create(['season_id' => $season->id]);

        $response = $this->post($this->progressStoreRoute($media, $season, $episode));

        // bootstrap/app.php renvoie 401 Inertia pour les non-authentifiés
        $response->assertStatus(401);
    }

    public function test_user_can_mark_episode_as_watched(): void
    {
        $user    = User::factory()->create();
        $media   = Media::factory()->create(['is_public' => true, 'approved' => true]);
        $season  = Season::factory()->create(['media_id' => $media->id]);
        $episode = Episode::factory()->create(['season_id' => $season->id]);

        $response = $this->actingAs($user)
                         ->post($this->progressStoreRoute($media, $season, $episode));

        $response->assertRedirect();
        $this->assertDatabaseHas('episode_user', [
            'user_id'    => $user->id,
            'episode_id' => $episode->id,
        ]);
    }

    public function test_mark_as_watched_returns_404_if_season_does_not_belong_to_media(): void
    {
        $user    = User::factory()->create();
        $media   = Media::factory()->create(['is_public' => true, 'approved' => true]);
        $other   = Media::factory()->create();
        $season  = Season::factory()->create(['media_id' => $other->id]);
        $episode = Episode::factory()->create(['season_id' => $season->id]);

        $response = $this->actingAs($user)
                         ->post($this->progressStoreRoute($media, $season, $episode));

        $response->assertNotFound();
    }

    public function test_user_can_unmark_episode_as_watched(): void
    {
        $user    = User::factory()->create();
        $media   = Media::factory()->create(['is_public' => true, 'approved' => true]);
        $season  = Season::factory()->create(['media_id' => $media->id]);
        $episode = Episode::factory()->create(['season_id' => $season->id]);

        $user->episodes()->attach($episode->id, ['watched_at' => now()]);

        $response = $this->actingAs($user)
                         ->delete($this->progressDestroyRoute($media, $season, $episode));

        $response->assertRedirect();
        $this->assertDatabaseMissing('episode_user', [
            'user_id'    => $user->id,
            'episode_id' => $episode->id,
        ]);
    }
}
