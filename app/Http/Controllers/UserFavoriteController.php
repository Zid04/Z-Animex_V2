<?php

namespace App\Http\Controllers;

use App\Http\Resources\FavoriteResource;
use App\Models\Media;

use App\Services\UserFavoriteService;

class UserFavoriteController extends Controller
{
    public function __construct(
        private UserFavoriteService $service
    ) {}

    public function index()
    {
        $favoritesQuery = $this->service->list(auth()->user());
        $favorites = $favoritesQuery->paginate(20)->withQueryString();
        
        // Construire les liens de pagination
        $links = [];
        if ($favorites->lastPage() > 1) {
            for ($i = 1; $i <= $favorites->lastPage(); $i++) {
                $links[] = [
                    'url' => $favorites->url($i),
                    'label' => (string) $i,
                    'active' => $i === $favorites->currentPage(),
                ];
            }
        }
        
        return inertia('favorites/index', [
            'favorites' => [
                'data' => FavoriteResource::collection($favorites->items())->resolve(),
                'links' => $links,
            ],
        ]);
    }

    public function store(Media $media)
    {
        $this->authorize('view', $media);
        $this->service->add(auth()->user(), $media);
        return back();
    }

    public function destroy(Media $media)
    {
        $this->service->remove(auth()->user(), $media);
        return back();
    }
}