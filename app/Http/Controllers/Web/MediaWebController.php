<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use App\Models\Tag;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class MediaWebController extends Controller
{
    use AuthorizesRequests;
    
    public function __construct(
        private MediaService $service
    ) {}

    /*
    |--------------------------------
    | INDEX
    |--------------------------------
    */

   public function index(Request $request)
{
    $user = Auth::user();

    $query = Media::query()
        ->with('tags')
        ->where(function ($q) use ($user) {
            // Médias publics et approuvés
            $q->where(function ($q) {
                $q->where('approved', true)
                  ->where('is_public', true);
            })
            // OU médias dont l'utilisateur est le créateur
            ->orWhere('user_id', $user->id)
            // OU médias dans la watchlist de l'utilisateur
            ->orWhereHas('userMedia', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        })
        ->when($request->search, fn($q) =>
            $q->where('title', 'like', '%' . $request->search . '%')
        )
        ->when($request->type, fn($q) =>
            $q->where('type', $request->type)
        )
        ->when($request->year, fn($q) =>
            $q->where('year', $request->year)
        )
        ->when($request->sort, fn($q) =>
            match ($request->sort) {
                'score'      => $q->orderByDesc('score'),
                'newest'     => $q->orderByDesc('year'),
                'oldest'     => $q->orderBy('year'),
                'rank'       => $q->orderBy('rank'),
                'popularity' => $q->orderByDesc('popularity'),
                default      => $q,
            }
        );

    return inertia('media/index', [
        'media'   => MediaResource::collection(
            $query->paginate(20)->withQueryString()
        ),
        'filters' => $request->only(['search', 'type', 'year', 'sort', 'status']),
        'tags'    => Tag::select('id', 'name')->orderBy('name')->get(),
        'years'   => Media::select('year')
            ->whereNotNull('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year'),
    ]);
}
    /*
    |--------------------------------
    | CREATE
    |--------------------------------
    */

    public function create()
    {
        return inertia('media/create', [
            'all_tags' => Tag::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /*
    |--------------------------------
    | STORE
    |--------------------------------
    */

    public function store(StoreMediaRequest $request)
    {
        $data = $request->validated();

        // Convertir studios string → tableau JSON
        if (isset($data['studios']) && is_string($data['studios'])) {
            $data['studios'] = array_map(
                fn($s) => ['name' => trim($s)],
                array_filter(explode(',', $data['studios']))
            );
        }

        $data['user_id']   = Auth::id();
        $data['approved']  = true;
        $data['is_public'] = $data['is_public'] ?? true;

        $media = $this->service->create($data);

        if (!$media->id) {
            return redirect()->route('media.index')
                ->with('error', 'Erreur lors de la création du média');
        }

        if (!empty($data['genres'])) {
            $media->tags()->sync($data['genres']);
        }

        return redirect()->route('media.show', ['media' => $media->id]);
    }

    /*
    |--------------------------------
    | SHOW
    |--------------------------------
    */

    public function show(Media $media)
    {
        $this->authorize('view', $media);

        // Vérification de sécurité : s'assurer que le média a un ID valide
        if (!$media || !$media->id) {
            abort(404, 'Média non trouvé');
        }

        $user  = Auth::user();
        $media = $this->service->show($media);
        $epIds = $user->episodes()->pluck('episodes.id')->toArray();

        $episodes = $media->seasons->flatMap->episodes;
        $total   = $episodes->count();
        $watched = $episodes->whereIn('id', $epIds)->count();

        return inertia('media/show', [
            'media'       => new MediaResource($media),
            'all_tags'    => Tag::select('id', 'name')->orderBy('name')->get(),
            'progress'    => [
                'episodes_total'   => $total,
                'episodes_watched' => $watched,
                'percent'          => $total > 0 ? round($watched / $total * 100) : 0,
            ],
            'user_rating' => $user->ratings()
                ->where('media_id', $media->id)
                ->value('rating'),
            'is_favorite' => $user->favorites()
                ->where('media_id', $media->id)
                ->exists(),
            'user_media'  => $user->userMedia()
                ->where('media_id', $media->id)
                ->first()?->only(['id','status', 'progress']),
            'auth'        => ['user' => ['id' => $user->id]],
            'can'         => [
                'update' => $user->can('update', $media),
                'delete' => $user->can('delete', $media),
            ],
        ]);
    }

    /*
    |--------------------------------
    | EDIT
    |--------------------------------
    */

    public function edit(Media $media)
    {
        $this->authorize('update', $media);

        return inertia('media/edit', [
            'media' => new MediaResource($media),
            'all_tags' => Tag::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /*
    |--------------------------------
    | UPDATE
    |--------------------------------
    */

    public function update(UpdateMediaRequest $request, Media $media)
    {
        $this->authorize('update', $media);

        $data = $request->validated();

        // Convertir studios string → tableau JSON
        if (isset($data['studios']) && is_string($data['studios'])) {
            $data['studios'] = array_map(
                fn($s) => ['name' => trim($s)],
                array_filter(explode(',', $data['studios']))
            );
        }

        $this->service->update($media, $data);

        return redirect()->route('media.show', ['media' => $media->id]);
    }

    /*
    |--------------------------------
    | DESTROY
    |--------------------------------
    */

    public function destroy(Media $media)
    {
        $this->authorize('delete', $media);

        $this->service->delete($media);

        return redirect()->route('media.index');
    }

    /*
    |--------------------------------
    | MY MEDIA
    |--------------------------------
    */

    public function mine()
    {
        $media = Media::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return inertia('media/my-media', [
            'media' => MediaResource::collection($media),
        ]);
    }
}
