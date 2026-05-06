<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Http\Resources\MediaResource;
use App\Services\MediaService;
use App\Models\Media;

class MediaController extends Controller
{
    public function __construct(
        private MediaService $service
    ) {}

    public function index()
    {
        return MediaResource::collection(
            $this->service->index(request())
        );
    }

    public function store(StoreMediaRequest $request)
    {
        return new MediaResource(
            $this->service->create($request->validated())
        );
    }


    public function show(Media $media)
    {
        return new MediaResource(
            $this->service->show($media)
        );
    }

    public function update(UpdateMediaRequest $request, Media $media)
    {
        $this->authorize('update', $media);

        return new MediaResource(
            $this->service->update($media, $request->validated())
        );
    }

    public function destroy(Media $media)
    {
        $this->authorize('delete', $media);

        $this->service->delete($media);

        return response()->json([
            'message' => 'Media deleted'
        ]);
    }
}