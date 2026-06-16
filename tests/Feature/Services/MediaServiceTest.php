<?php

namespace Tests\Feature\Services;

use App\Models\Media;
use App\Models\User;
use App\Services\MediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class MediaServiceTest extends TestCase
{
    use RefreshDatabase;

    private MediaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MediaService();
    }

    public function test_index_returns_paginated_media_owned_by_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Media::factory()->count(5)->create(['user_id' => $user->id, 'is_public' => false]);

        $request = Request::create('/api/media', 'GET');
        $result  = $this->service->index($request);

        $this->assertEquals(5, $result->total());
    }

    public function test_index_filters_by_search_title(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Media::factory()->create(['title' => 'Attack on Titan', 'user_id' => $user->id, 'is_public' => false]);
        Media::factory()->create(['title' => 'Death Note', 'user_id' => $user->id, 'is_public' => false]);

        $request = Request::create('/api/media', 'GET', ['search' => 'Attack']);
        $result  = $this->service->index($request);

        $this->assertEquals(1, $result->total());
        $this->assertEquals('Attack on Titan', $result->items()[0]->title);
    }

    public function test_index_filters_by_type(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Media::factory()->create(['type' => 'anime', 'user_id' => $user->id, 'is_public' => false]);
        Media::factory()->create(['type' => 'movie', 'user_id' => $user->id, 'is_public' => false]);

        $request = Request::create('/api/media', 'GET', ['type' => 'anime']);
        $result  = $this->service->index($request);

        $this->assertEquals(1, $result->total());
    }

    public function test_index_filters_by_year(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Media::factory()->create(['year' => 2020, 'user_id' => $user->id, 'is_public' => false]);
        Media::factory()->create(['year' => 2023, 'user_id' => $user->id, 'is_public' => false]);

        $request = Request::create('/api/media', 'GET', ['year' => 2020]);
        $result  = $this->service->index($request);

        $this->assertEquals(1, $result->total());
    }

    public function test_index_filters_by_status(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Media::factory()->create(['status' => 'Finished Airing', 'user_id' => $user->id, 'is_public' => false]);
        Media::factory()->create(['status' => 'Currently Airing', 'user_id' => $user->id, 'is_public' => false]);

        $request = Request::create('/api/media', 'GET', ['status' => 'Finished Airing']);
        $result  = $this->service->index($request);

        $this->assertEquals(1, $result->total());
    }

    public function test_index_filters_by_airing(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Media::factory()->airing()->create(['user_id' => $user->id, 'is_public' => false]);
        Media::factory()->create(['airing' => false, 'user_id' => $user->id, 'is_public' => false]);

        $request = Request::create('/api/media', 'GET', ['airing' => '1']);
        $result  = $this->service->index($request);

        $this->assertEquals(1, $result->total());
    }

    public function test_index_paginates_by_20(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Media::factory()->count(25)->create(['user_id' => $user->id, 'is_public' => false]);

        $request = Request::create('/api/media', 'GET');
        $result  = $this->service->index($request);

        $this->assertCount(20, $result->items());
        $this->assertEquals(25, $result->total());
    }

    public function test_show_loads_media_with_all_relations(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create(['is_public' => true, 'user_id' => $user->id]);
        $this->actingAs($user);

        $result = $this->service->show($media);

        $this->assertTrue($result->relationLoaded('seasons'));
        $this->assertTrue($result->relationLoaded('tags'));
        $this->assertTrue($result->relationLoaded('comments'));
        $this->assertTrue($result->relationLoaded('ratings'));
    }

    public function test_create_persists_media_to_database(): void
    {
        $user = User::factory()->create();
        $data = Media::factory()->make(['user_id' => $user->id])->toArray();

        $media = $this->service->create($data);

        $this->assertInstanceOf(Media::class, $media);
        $this->assertDatabaseHas('media', ['id' => $media->id, 'title' => $data['title']]);
    }

    public function test_update_changes_media_fields(): void
    {
        $media = Media::factory()->create(['title' => 'Old Title']);

        $result = $this->service->update($media, ['title' => 'New Title']);

        $this->assertEquals('New Title', $result->title);
        $this->assertDatabaseHas('media', ['id' => $media->id, 'title' => 'New Title']);
    }

    public function test_delete_removes_media_from_database(): void
    {
        $media = Media::factory()->create();

        $this->service->delete($media);

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }
}
