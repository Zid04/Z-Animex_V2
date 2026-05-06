<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Action',
            'Romance',
            'Comédie',
            'Drame',
            'Fantaisie',
            'Science-Fiction',
            'Horreur',
            'Aventure',
            'Thriller',
            'Mystère',
            'Animation',
            'Documentaire',
        ];

        foreach ($tags as $name) {
            Tag::firstOrCreate(['name' => $name]);
        }

        $this->command->info(count($tags) . ' tags seedés.');
    }
}