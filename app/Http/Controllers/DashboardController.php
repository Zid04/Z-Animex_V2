<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
   public function index(Request $request)
{
    $user = Auth::user();

    $allMedia = Media::query()
        ->with('episodes')
        ->where('approved', true)
        ->get();

    $totalMedia = $allMedia->count();

    $animeCount = $allMedia->where('type', 'anime')->count();
    $movieCount = $allMedia->where('type', 'movie')->count();
    $seriesCount = $allMedia->where('type', 'series')->count();

    $watchedEpisodeIds = $user->episodes()->pluck('episode_id')->toArray();

    $completed = 0;
    $inProgress = 0;
    $notStarted = 0;

    foreach ($allMedia as $media) {
      $total = $media->episodes()->count();

$watched = $media->episodes()
    ->whereIn('id', $watchedEpisodeIds)
    ->count();


        if ($total === 0 || $watched === 0) {
            $notStarted++;
        } elseif ($watched === $total) {
            $completed++;
        } else {
            $inProgress++;
        }
    }

    $latestMedia = $allMedia->sortByDesc('created_at')->take(6);

    return inertia('dashboard', [
        'stats' => [
            'total'       => $totalMedia,
            'anime'       => $animeCount,
            'movies'      => $movieCount,
            'series'      => $seriesCount,
            'completed'   => $completed,
            'in_progress' => $inProgress,
            'not_started' => $notStarted,
        ],
        'latest_media' => \App\Http\Resources\MediaResource::collection($latestMedia),
    ]);
}
}