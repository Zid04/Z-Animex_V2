<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Media;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller
{
    use AuthorizesRequests;
    /*
    |--------------------------------
    | STORE
    |--------------------------------
    */
    public function store(StoreCommentRequest $request, Media $media)
    {
        $this->authorize('create', [Comment::class, $media]);

        $comment = $media->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->validated()['content'],
        ]);

        // Charger la relation user si besoin pour le flash ou le debug
        $comment->load('user');

        return back()->with('success', 'Commentaire enregistré');
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

        $comment->load('user');

        return back()->with('success', 'Commentaire mis à jour');
    }

    /*
    |--------------------------------
    | DELETE
    |--------------------------------
    */
  public function destroy(Media $media, Comment $comment)
{
    $this->authorize('delete', $comment);
    $comment->delete();
    return back()->with('success', 'Commentaire supprimé');
}

    /*
    |--------------------------------
    | LIST MEDIA COMMENTS
    |--------------------------------
    */
    public function index(Media $media)
    {
        $comment = $media->comments()
            ->with('user')
            ->latest()
            ->paginate(20);

        return CommentResource::collection($comment);
    }
}
