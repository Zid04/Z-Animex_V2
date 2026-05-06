<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Media;

class CommentController extends Controller
{
    /*
    |--------------------------------
    | STORE
    |--------------------------------
    */

    public function store(StoreCommentRequest $request, Media $media)
    {
        $comment = $media->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->validated()['content'],
        ]);

        return new CommentResource(
            $comment->load('user')
        );
    }

    /*
    |--------------------------------
    | UPDATE
    |--------------------------------
    */

    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $comment->update([
            'content' => $request->validated()['content'],
        ]);

      return new CommentResource($comment->load('user'));
    }

    /*
    |--------------------------------
    | DELETE
    |--------------------------------
    */

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted'
        ]);
    }

    /*
    |--------------------------------
    | LIST MEDIA COMMENTS
    |--------------------------------
    */

    public function index(Media $media)
    {
        $comments = $media->comments()
            ->with('user')
            ->latest()
            ->paginate(20);

        return CommentResource::collection($comments);
    }
}