<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Media;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $path = resource_path('data/anime.csv');

        if (! file_exists($path)) {
            $this->command->error("Fichier introuvable : {$path}");
            return;
        }

        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        $count  = 0;
        $errors = 0;

        foreach ($csv->getRecords() as $data) {
            try {
                $images  = $this->parseJson($data['images']);
$studios = $this->parseJson($data['studios']);
$genres  = $this->parseJson($data['genres']);

$cover = $images['jpg']['image_url'] ?? null; // ← remettre

Media::updateOrCreate(
    ['external_id' => (int) $data['mal_id']],
    [
         'user_id'    => 1,
        'title'      => $data['title'],
        'type'       => $this->mapType($data['type']),
        'source'     => $data['source'] ?: null,
        'status'     => $data['status'] ?: null,
        'airing'     => in_array(strtolower(trim($data['airing'])), ['true', '1', 'yes']),
        'approved'   => in_array(strtolower(trim($data['approved'])), ['true', '1', 'yes']),
        'is_public'  => true,
        'episodes'   => $data['episodes'] ? (int) $data['episodes'] : null,
        'duration'   => $data['duration'] ?: null,
        'score'      => $data['score'] ? (float) $data['score'] : null,
        'scored_by'  => $data['scored_by'] ? (int) $data['scored_by'] : null,
        'rank'       => $data['rank'] ? (int) $data['rank'] : null,
        'popularity' => $data['popularity'] ? (int) $data['popularity'] : null,
        'members'    => $data['members'] ? (int) $data['members'] : null,
        'favorites'  => $data['favorites'] ? (int) $data['favorites'] : null,
        'year'       => $data['year'] ? (int) $data['year'] : null,
        'cover'      => $cover,                
        'images'     => $images,
        'studios'    => $studios,
        'genres'     => $genres,
    ]
);

                $count++;

            } catch (\Throwable $e) {
                $errors++;
                Log::warning("MediaSeeder: échec sur mal_id={$data['mal_id']} — {$e->getMessage()}");
            }
        }

        $this->command->info("MediaSeeder terminé : {$count} importés, {$errors} erreurs.");
    }

    private function mapType(string $type): string
    {
        return match (strtolower($type)) {
            'movie'                              => 'movie',
            'tv', 'ova', 'ona', 'special', 'music' => 'anime',
            default                              => 'anime',
        };
    }

    private function parseJson(string $value): ?array
    {
        if (empty($value) || $value === '[]') {
            return [];
        }

        $value = trim($value, '"');

        $json = str_replace(
            ["'", 'True', 'False', 'None'],
            ['"', 'true', 'false', 'null'],
            $value
        );

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : null;
    }
}