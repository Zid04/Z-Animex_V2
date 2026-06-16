<?php

namespace Tests\Feature\Web;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests les routes /api/media (session-based, dans web.php sous middleware auth+verified)
 */
class MediaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_media_list(): void
    {
        $response = $this->getJson('/api/media');

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_media(): void
    {
        $user = User::factory()->create();
        Media::factory()->count(3)->create(['user_id' => $user->id, 'is_public' => false]);

        $response = $this->actingAs($user)->getJson('/api/media');

        $response->assertOk()
                 ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_authenticated_user_can_create_media(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/media', [
            'title' => 'Fullmetal Alchemist',
            'type'  => 'anime',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('media', ['title' => 'Fullmetal Alchemist']);
    }

    public function test_create_media_requires_title_and_type(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/media', []);

        $response->assertUnprocessable()
                 ->assertJsonValidationErrors(['title', 'type']);
    }

    public function test_create_media_validates_type_enum(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/media', [
            'title' => 'Test',
            'type'  => 'manga',
        ]);

        $response->assertUnprocessable()
                 ->assertJsonValidationErrors(['type']);
    }

    public function test_authenticated_user_can_view_public_media(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create(['is_public' => true, 'approved' => true]);

        $response = $this->actingAs($user)->getJson("/api/media/{$media->id}");

        // JsonResource::withoutWrapping() est actif (AppServiceProvider) → pas de clé 'data'
        $response->assertOk()
                 ->assertJsonPath('id', $media->id);
    }

    public function test_owner_can_update_their_media(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->putJson("/api/media/{$media->id}", [
            'title' => 'Updated Title',
            'type'  => $media->type,
        ]);

        $response->assertOk()
                 ->assertJsonPath('title', 'Updated Title');
    }

    public function test_non_owner_cannot_update_media(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $media = Media::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($other)->putJson("/api/media/{$media->id}", [
            'title' => 'Hacked',
            'type'  => $media->type,
        ]);

        $response->assertForbidden();
    }

    public function test_owner_can_delete_their_media(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/media/{$media->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    public function test_non_owner_cannot_delete_media(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $media = Media::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($other)->deleteJson("/api/media/{$media->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('media', ['id' => $media->id]);
    }
}
