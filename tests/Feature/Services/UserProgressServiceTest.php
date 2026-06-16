<?php

namespace Tests\Feature\Services;

use App\Models\Episode;
use App\Models\Season;
use App\Models\User;
use App\Services\UserProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProgressServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserProgressService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserProgressService();
    }

    public function test_mark_as_watched_creates_episode_user_pivot(): void
    {
        $user    = User::factory()->create();
        $episode = Episode::factory()->create();

        $this->service->markAsWatched($user, $episode);

        $this->assertDatabaseHas('episode_user', [
            'user_id'    => $user->id,
            'episode_id' => $episode->id,
        ]);
    }

    public function test_mark_as_watched_stores_watched_at_timestamp(): void
    {
        $user    = User::factory()->create();
        $episode = Episode::factory()->create();

        $this->service->markAsWatched($user, $episode);

        $pivot = $user->episodes()->where('episode_id', $episode->id)->first();

        $this->assertNotNull($pivot->pivot->watched_at);
    }

    public function test_marking_same_episode_twice_does_not_duplicate(): void
    {
        $user    = User::factory()->create();
        $episode = Episode::factory()->create();

        $this->service->markAsWatched($user, $episode);
        $this->service->markAsWatched($user, $episode);

        $this->assertDatabaseCount('episode_user', 1);
    }

    public function test_unmark_as_watched_removes_pivot_entry(): void
    {
        $user    = User::factory()->create();
        $episode = Episode::factory()->create();

        $this->service->markAsWatched($user, $episode);
        $this->service->unmarkAsWatched($user, $episode);

        $this->assertDatabaseMissing('episode_user', [
            'user_id'    => $user->id,
            'episode_id' => $episode->id,
        ]);
    }

    public function test_unmark_on_unwatched_episode_does_nothing(): void
    {
        $user    = User::factory()->create();
        $episode = Episode::factory()->create();

        $this->service->unmarkAsWatched($user, $episode);

        $this->assertDatabaseMissing('episode_user', [
            'user_id'    => $user->id,
            'episode_id' => $episode->id,
        ]);
    }

    public function test_multiple_users_can_watch_same_episode(): void
    {
        $user1   = User::factory()->create();
        $user2   = User::factory()->create();
        $episode = Episode::factory()->create();

        $this->service->markAsWatched($user1, $episode);
        $this->service->markAsWatched($user2, $episode);

        $this->assertDatabaseCount('episode_user', 2);
    }
}
