<?php

namespace Tests\Feature\Web;

use App\Models\Media;
use App\Models\User;
use App\Models\UserRating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_rate_media(): void
    {
        $media = Media::factory()->create(['is_public' => true, 'approved' => true]);

        $response = $this->post(route('ratings.store', $media), ['rating' => 8]);

        // bootstrap/app.php renvoie 401 Inertia pour les non-authentifiés
        $response->assertStatus(401);
    }

    public function test_user_can_rate_media(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create(['is_public' => true, 'approved' => true]);

        $response = $this->actingAs($user)->post(route('ratings.store', $media), ['rating' => 9]);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_ratings', [
            'user_id'  => $user->id,
            'media_id' => $media->id,
            'rating'   => 9,
        ]);
    }

    public function test_rating_must_be_provided(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create(['is_public' => true, 'approved' => true]);

        $response = $this->actingAs($user)->post(route('ratings.store', $media), []);

        $response->assertSessionHasErrors('rating');
    }

    public function test_user_can_update_their_rating(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create(['is_public' => true, 'approved' => true]);

        $this->actingAs($user)->post(route('ratings.store', $media), ['rating' => 5]);
        $this->actingAs($user)->post(route('ratings.store', $media), ['rating' => 8]);

        $this->assertDatabaseCount('user_ratings', 1);
        $this->assertDatabaseHas('user_ratings', ['rating' => 8]);
    }

    public function test_user_can_delete_their_rating(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create(['is_public' => true, 'approved' => true]);

        UserRating::factory()->create(['user_id' => $user->id, 'media_id' => $media->id]);

        $response = $this->actingAs($user)->delete(route('ratings.destroy', $media));

        $response->assertRedirect();
        $this->assertDatabaseMissing('user_ratings', [
            'user_id'  => $user->id,
            'media_id' => $media->id,
        ]);
    }

    public function test_delete_rating_returns_404_if_no_rating_exists(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create(['is_public' => true, 'approved' => true]);

        $response = $this->actingAs($user)->delete(route('ratings.destroy', $media));

        $response->assertNotFound();
    }
}
