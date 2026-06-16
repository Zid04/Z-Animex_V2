<?php

namespace Tests\Feature\Web;

use App\Models\Media;
use App\Models\User;
use App\Models\UserFavorite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_favorites_page(): void
    {
        // bootstrap/app.php overrides le handler AuthenticationException
        // pour renvoyer Inertia 401 au lieu d'un redirect login.
        $response = $this->get(route('favorites.index'));

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_view_favorites_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('favorites.index'));

        $response->assertOk();
    }

    public function test_user_can_add_media_to_favorites(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create(['is_public' => true, 'approved' => true]);

        $response = $this->actingAs($user)->post(route('favorites.store', $media));

        $response->assertRedirect();
        $this->assertDatabaseHas('user_favorites', [
            'user_id'  => $user->id,
            'media_id' => $media->id,
        ]);
    }

    public function test_user_cannot_add_duplicate_favorite(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create(['is_public' => true, 'approved' => true]);

        UserFavorite::factory()->create(['user_id' => $user->id, 'media_id' => $media->id]);

        $response = $this->actingAs($user)->post(route('favorites.store', $media));

        $response->assertStatus(422);
    }

    public function test_user_can_remove_media_from_favorites(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create(['is_public' => true, 'approved' => true]);

        UserFavorite::factory()->create(['user_id' => $user->id, 'media_id' => $media->id]);

        $response = $this->actingAs($user)->delete(route('favorites.destroy', $media));

        $response->assertRedirect();
        $this->assertDatabaseMissing('user_favorites', [
            'user_id'  => $user->id,
            'media_id' => $media->id,
        ]);
    }

    public function test_guest_cannot_add_to_favorites(): void
    {
        $media = Media::factory()->create();

        $response = $this->post(route('favorites.store', $media));

        $response->assertStatus(401);
    }
}
