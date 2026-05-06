<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttachTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Media;
use App\Models\Tag;
use App\Services\TagService;

class TagController extends Controller
{
    public function __construct(
        private TagService $service
    ) {}

    /*
    |--------------------------------
    | ATTACH TAG
    |--------------------------------
    */

    public function attach(AttachTagRequest $request, Media $media)
    {
        $this->authorize('update', $media); // TagPolicy indirect via MediaPolicy

        $tag = $this->service->attach(
            $media,
            $request->validated()['tag_id']
        );

        return new TagResource($tag);
    }

    /*
    |--------------------------------
    | DETACH TAG
    |--------------------------------
    */

    public function detach(Media $media, Tag $tag)
    {
        $this->authorize('update', $media);

        $this->service->detach($media, $tag);

        return response()->json([
            'message' => 'Tag détaché avec succès',
        ]);
    }
}