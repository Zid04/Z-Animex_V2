<?php

namespace Tests\Feature\Services;

use App\Models\Media;
use App\Models\User;
use App\Models\UserRating;
use App\Services\UserRatingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRatingServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserRatingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserRatingService();
    }

    public function test_set_rating_creates_new_rating(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create();

        $rating = $this->service->setRating($user->id, $media->id, 8);

        $this->assertInstanceOf(UserRating::class, $rating);
        $this->assertEquals(8, $rating->rating);
        $this->assertDatabaseHas('user_ratings', [
            'user_id'  => $user->id,
            'media_id' => $media->id,
            'rating'   => 8,
        ]);
    }

    public function test_set_rating_updates_existing_rating(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create();

        $this->service->setRating($user->id, $media->id, 5);
        $updated = $this->service->setRating($user->id, $media->id, 9);

        $this->assertEquals(9, $updated->rating);
        $this->assertDatabaseCount('user_ratings', 1);
        $this->assertDatabaseHas('user_ratings', ['rating' => 9]);
    }

    public function test_set_rating_does_not_create_duplicate_rows(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create();

        $this->service->setRating($user->id, $media->id, 7);
        $this->service->setRating($user->id, $media->id, 3);

        $this->assertDatabaseCount('user_ratings', 1);
    }

    public function test_delete_rating_removes_entry(): void
    {
        $rating = UserRating::factory()->create();

        $this->service->deleteRating($rating);

        $this->assertDatabaseMissing('user_ratings', ['id' => $rating->id]);
    }

    public function test_different_users_can_each_rate_same_media(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $media = Media::factory()->create();

        $this->service->setRating($user1->id, $media->id, 8);
        $this->service->setRating($user2->id, $media->id, 6);

        $this->assertDatabaseCount('user_ratings', 2);
    }
}
