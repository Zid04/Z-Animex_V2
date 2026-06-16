<?php

namespace Tests\Feature\Services;

use App\Models\Comment;
use App\Models\Media;
use App\Models\User;
use App\Services\CommentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentServiceTest extends TestCase
{
    use RefreshDatabase;

    private CommentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CommentService();
    }

    public function test_create_persists_comment_on_media(): void
    {
        $user  = User::factory()->create();
        $media = Media::factory()->create();

        $comment = $this->service->create($media, $user->id, ['content' => 'Great anime!']);

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals('Great anime!', $comment->content);
        $this->assertDatabaseHas('comments', [
            'media_id' => $media->id,
            'user_id'  => $user->id,
            'content'  => 'Great anime!',
        ]);
    }

    public function test_create_associates_comment_with_correct_media(): void
    {
        $user   = User::factory()->create();
        $media1 = Media::factory()->create();
        $media2 = Media::factory()->create();

        $this->service->create($media1, $user->id, ['content' => 'On media 1']);
        $this->service->create($media2, $user->id, ['content' => 'On media 2']);

        $this->assertDatabaseHas('comments', ['media_id' => $media1->id, 'content' => 'On media 1']);
        $this->assertDatabaseHas('comments', ['media_id' => $media2->id, 'content' => 'On media 2']);
    }

    public function test_update_changes_comment_content(): void
    {
        $comment = Comment::factory()->create(['content' => 'Original content']);

        $result = $this->service->update($comment, ['content' => 'Updated content']);

        $this->assertInstanceOf(Comment::class, $result);
        $this->assertEquals('Updated content', $comment->fresh()->content);
        $this->assertDatabaseHas('comments', ['id' => $comment->id, 'content' => 'Updated content']);
    }

    public function test_update_does_not_change_user_or_media(): void
    {
        $comment = Comment::factory()->create(['content' => 'Original']);

        $this->service->update($comment, ['content' => 'Updated']);

        $fresh = $comment->fresh();
        $this->assertEquals($comment->user_id, $fresh->user_id);
        $this->assertEquals($comment->media_id, $fresh->media_id);
    }

    public function test_delete_removes_comment_from_database(): void
    {
        $comment = Comment::factory()->create();

        $result = $this->service->delete($comment);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
