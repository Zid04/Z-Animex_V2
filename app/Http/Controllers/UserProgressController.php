<?php
namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Season;
use App\Models\Episode;
use App\Services\UserProgressService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserProgressController extends Controller
{
    use AuthorizesRequests;
    
    public function store(
        Media $media,
        Season $season,
        Episode $episode,
        UserProgressService $progressService
    ) {
        $this->authorize('view', $media);

        abort_unless($season->media_id === $media->id, 404);
        abort_unless($episode->season_id === $season->id, 404);

        $progressService->markAsWatched(Auth::user(), $episode);

        return back();
    }

    public function destroy(
        Media $media,
        Season $season,
        Episode $episode,
        UserProgressService $progressService
    ) {
        $this->authorize('view', $media);

        abort_unless($season->media_id === $media->id, 404);
        abort_unless($episode->season_id === $season->id, 404);

        $progressService->unmarkAsWatched(Auth::user(), $episode);

        return back();
    }
}