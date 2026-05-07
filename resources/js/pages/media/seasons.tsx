import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';

type Episode = { id: number; number: number; title: string; watched: boolean };
type Season  = { id: number; number: number; episodes: Episode[] };

type Props = {
    media: { id: number; title: string; type: string; year?: number };
    seasons: Season[];
};

export default function MediaSeasons({ media, seasons }: Props) {
    return (
        <AppLayout>
            <Head title={`${media.title} — Saisons`} />

            <div className="max-w-4xl mx-auto py-6 px-4">
                <div className="mb-6 flex items-center gap-4">
                    <Link href={`/media/${media.id}`}>
                        <Button variant="ghost" size="icon">
                            <ArrowLeft className="w-5 h-5" />
                        </Button>
                    </Link>
                    <div>
                        <h1 className="text-3xl font-bold">{media.title}</h1>
                        <p className="text-sm text-muted-foreground">
                            {media.type}
                            {media.year && ` • ${media.year}`}
                            {` • ${seasons.length} saison${seasons.length > 1 ? 's' : ''}`}
                        </p>
                    </div>
                </div>

                <div className="space-y-8">
                    {seasons.length === 0 ? (
                        <p className="text-center text-muted-foreground py-12">Aucune saison disponible</p>
                    ) : (
                        seasons.map(season => (
                            <div key={season.id} className="border rounded-lg p-6">
                                <h2 className="text-xl font-bold mb-4">Saison {season.number}</h2>
                                <div className="space-y-2">
                                    {season.episodes.length === 0 ? (
                                        <p className="text-muted-foreground">Aucun épisode</p>
                                    ) : (
                                        season.episodes.map(episode => (
                                            <div
                                                key={episode.id}
                                                className="flex items-center gap-4 p-3 hover:bg-muted rounded-md transition"
                                            >
                                                <p className={`font-medium flex-1 ${episode.watched ? 'line-through text-muted-foreground' : ''}`}>
                                                    Épisode {episode.number}
                                                    {episode.title && ` : ${episode.title}`}
                                                </p>
                                                {episode.watched && (
                                                    <span className="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
                                                        Regardé
                                                    </span>
                                                )}
                                            </div>
                                        ))
                                    )}
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </div>
        </AppLayout>
    );
}