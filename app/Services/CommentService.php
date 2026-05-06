<?php
namespace App\Services;

use App\Models\Comment;
use App\Models\Media;

class CommentService
{
    /*
    |--------------------------------
    | CREATE COMMENT
    |--------------------------------
    */

    public function create(Media $media, int $userId, array $data): Comment
    {
        return $media->comments()->create([
            'user_id' => $userId,
            'content' => $data['content'],
        ]);
    }

    /*
    |--------------------------------
    | UPDATE COMMENT
    |--------------------------------
    */

    public function update(Comment $comment, array $data): Comment
    {
        $comment->update([
            'content' => $data['content'],
        ]);

        return $comment;
    }

    /*
    |--------------------------------
    | DELETE COMMENT
    |--------------------------------
    */

    public function delete(Comment $comment): bool
    {
        return $comment->delete();
    }
}