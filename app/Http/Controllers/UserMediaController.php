<?php

namespace App\Http\Controllers;

use App\Models\UserMedia;
use App\Services\UserMediaService;
use App\Http\Requests\StoreUserMediaRequest;
use App\Http\Requests\UpdateUserMediaRequest;
use App\Http\Resources\UserMediaResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserMediaController extends Controller
{
    use AuthorizesRequests;
    
    public function index()
    {
        $userMedia = Auth::user()
            ->userMedia()
            ->with('media')
            ->latest()
            ->paginate(20);

        return inertia('watchlist/index', [
            'watchlist' => UserMediaResource::collection($userMedia),
        ]);
    }

    public function store(StoreUserMediaRequest $request, UserMediaService $service)
    {
        $this->authorize('create', UserMedia::class);

        $service->create(Auth::user(), $request->validated());

        return back();
    }

    public function update(UpdateUserMediaRequest $request, UserMedia $userMedia, UserMediaService $service)
    {
        $this->authorize('update', $userMedia);

        $service->update($userMedia, $request->validated());

        return back();
    }

    public function destroy(UserMedia $userMedia, UserMediaService $service)
    {
        $this->authorize('delete', $userMedia);

        $service->delete($userMedia);

        return back();
    }
}