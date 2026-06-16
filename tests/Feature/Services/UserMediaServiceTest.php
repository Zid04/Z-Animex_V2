<?php

namespace Tests\Feature\Services;

use App\Models\Media;
use App\Models\User;
use App\Models\UserMedia;
use App\Services\UserMediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserMediaServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserMediaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserMediaService();
    }

    public function test_create_adds_media_to_watchlist(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create();

        $entry = $this->service->create($user, ['media_id' => $media->id, 'status' => 'watching']);

        $this->assertInstanceOf(UserMedia::class, $entry);
        $this->assertEquals('watching', $entry->status);
        $this->assertDatabaseHas('user_media', [
            'user_id'  => $user->id,
            'media_id' => $media->id,
            'status'   => 'watching',
        ]);
    }

    public function test_create_defaults_status_to_planned(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create();

        $entry = $this->service->create($user, ['media_id' => $media->id]);

        $this->assertEquals('planned', $entry->status);
    }

    public function test_create_updates_status_if_entry_already_exists(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create();

        $this->service->create($user, ['media_id' => $media->id, 'status' => 'planned']);
        $updated = $this->service->create($user, ['media_id' => $media->id, 'status' => 'watching']);

        $this->assertEquals('watching', $updated->status);
        $this->assertDatabaseCount('user_media', 1);
    }

    public function test_update_changes_watchlist_status(): void
    {
        $entry = UserMedia::factory()->create(['status' => 'planned']);

        $this->service->update($entry, ['status' => 'watching']);

        $this->assertEquals('watching', $entry->fresh()->status);
        $this->assertDatabaseHas('user_media', ['id' => $entry->id, 'status' => 'watching']);
    }

    public function test_delete_removes_entry_from_watchlist(): void
    {
        $entry = UserMedia::factory()->create();

        $this->service->delete($entry);

        $this->assertDatabaseMissing('user_media', ['id' => $entry->id]);
    }

    public function test_mark_as_completed_sets_status_and_timestamp(): void
    {
        $entry = UserMedia::factory()->create(['status' => 'watching', 'completed_at' => null]);

        $result = $this->service->markAsCompleted($entry);

        $this->assertEquals('completed', $result->status);
        $this->assertNotNull($result->completed_at);
        $this->assertDatabaseHas('user_media', ['id' => $entry->id, 'status' => 'completed']);
    }

    public function test_mark_as_completed_returns_updated_model(): void
    {
        $entry = UserMedia::factory()->create(['status' => 'watching']);

        $returned = $this->service->markAsCompleted($entry);

        $this->assertInstanceOf(UserMedia::class, $returned);
        $this->assertEquals($entry->id, $returned->id);
    }
}
