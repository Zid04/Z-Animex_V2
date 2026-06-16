<?php

namespace Tests\Unit\Models;

use App\Models\Media;
use App\Models\User;
use App\Models\UserRating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_owned_by_returns_true_for_owner(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($media->isOwnedBy($user));
    }

    public function test_is_owned_by_returns_false_for_non_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $media = Media::factory()->create(['user_id' => $owner->id]);

        $this->assertFalse($media->isOwnedBy($other));
    }

    public function test_average_rating_returns_null_when_no_ratings(): void
    {
        $media = Media::factory()->create();

        $this->assertNull($media->averageRating());
    }

    public function test_average_rating_calculates_correctly(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create();

        UserRating::factory()->create(['user_id' => $user->id, 'media_id' => $media->id, 'rating' => 8]);
        UserRating::factory()->create(['user_id' => User::factory()->create()->id, 'media_id' => $media->id, 'rating' => 6]);

        $this->assertEquals(7.0, $media->averageRating());
    }

    public function test_boolean_fields_are_cast_correctly(): void
    {
        $media = Media::factory()->create([
            'airing'    => true,
            'approved'  => true,
            'is_public' => false,
        ]);

        $fresh = $media->fresh();

        $this->assertIsBool($fresh->airing);
        $this->assertIsBool($fresh->approved);
        $this->assertIsBool($fresh->is_public);
        $this->assertTrue($fresh->airing);
        $this->assertFalse($fresh->is_public);
    }

    public function test_array_fields_are_cast_correctly(): void
    {
        $media = Media::factory()->create([
            'images'  => ['jpg' => ['image_url' => 'https://placehold.co/300x400.jpg']],
            'studios' => [['name' => 'MAPPA']],
            'genres'  => [['name' => 'Action']],
        ]);

        $fresh = $media->fresh();

        $this->assertIsArray($fresh->images);
        $this->assertIsArray($fresh->studios);
        $this->assertIsArray($fresh->genres);
        $this->assertEquals('MAPPA', $fresh->studios[0]['name']);
    }

    public function test_media_has_correct_fillable_fields(): void
    {
        $media = new Media();

        $this->assertContains('title', $media->getFillable());
        $this->assertContains('type', $media->getFillable());
        $this->assertContains('user_id', $media->getFillable());
        $this->assertContains('is_public', $media->getFillable());
    }

    public function test_user_relation_returns_correct_user(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $media->user->id);
    }
}
