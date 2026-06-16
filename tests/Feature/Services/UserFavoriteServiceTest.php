<?php

namespace Tests\Feature\Services;

use App\Models\Media;
use App\Models\User;
use App\Models\UserFavorite;
use App\Services\UserFavoriteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class UserFavoriteServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserFavoriteService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserFavoriteService();
    }

    public function test_add_creates_favorite_entry(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create();

        $this->service->add($user, $media);

        $this->assertDatabaseHas('user_favorites', [
            'user_id'  => $user->id,
            'media_id' => $media->id,
        ]);
    }

    public function test_add_throws_422_when_already_favorited(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create();

        UserFavorite::factory()->create(['user_id' => $user->id, 'media_id' => $media->id]);

        // HttpException::getCode() retourne 0 (code PHP générique),
        // le code HTTP est dans getStatusCode().
        try {
            $this->service->add($user, $media);
            $this->fail('Une HttpException 422 aurait dû être levée.');
        } catch (HttpException $e) {
            $this->assertEquals(422, $e->getStatusCode());
        }
    }

    public function test_remove_deletes_favorite_entry(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create();

        UserFavorite::factory()->create(['user_id' => $user->id, 'media_id' => $media->id]);

        $this->service->remove($user, $media);

        $this->assertDatabaseMissing('user_favorites', [
            'user_id'  => $user->id,
            'media_id' => $media->id,
        ]);
    }

    public function test_remove_on_non_existing_favorite_does_nothing(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create();

        $this->service->remove($user, $media);

        $this->assertDatabaseMissing('user_favorites', [
            'user_id'  => $user->id,
            'media_id' => $media->id,
        ]);
    }

    public function test_list_returns_query_for_user_favorites(): void
    {
        $user   = User::factory()->create();
        $other  = User::factory()->create();
        $media1 = Media::factory()->create();
        $media2 = Media::factory()->create();

        UserFavorite::factory()->create(['user_id' => $user->id, 'media_id' => $media1->id]);
        UserFavorite::factory()->create(['user_id' => $user->id, 'media_id' => $media2->id]);
        UserFavorite::factory()->create(['user_id' => $other->id, 'media_id' => $media1->id]);

        $result = $this->service->list($user)->get();

        $this->assertCount(2, $result);
    }
}
