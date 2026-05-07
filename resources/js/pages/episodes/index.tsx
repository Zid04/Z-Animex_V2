import { Head, Link, router } from '@inertiajs/react';

import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';

type Episode = {
    id: number;
    number: number;
    title?: string;
    duration?: number;
    watched: boolean;
};

type Props = {
    media: { id: number; title: string };
    season: { id: number; number: number };
    episodes: Episode[];
};

export default function EpisodesIndex({ media, season, episodes }: Props) {
    function handleDestroy(episodeId: number) {
        if (!confirm('Supprimer cet épisode ?')) return;
        router.delete(`/episodes/${episodeId}`);
    }

    return (
        <AppLayout>
            <Head title={`Épisodes — Saison ${season.number} — ${media.title}`} />

            <div className="flex items-center justify-between mb-6">
                <h1 className="text-2xl font-semibold">
                    Épisodes — Saison {season.number}
                </h1>

                {/* ✅ ROUTE FRONT */}
                <Link href={`/media/${media.id}/seasons/${season.id}/episodes/create`}>
                    <Button>Ajouter un épisode</Button>
                </Link>
            </div>

            <div className="space-y-4">
                {episodes.length === 0 ? (
                    <p className="text-muted-foreground">Aucun épisode disponible.</p>
                ) : (
                    episodes.map((ep) => (
                        <div
                            key={ep.id}
                            className="border rounded p-4 flex justify-between items-center"
                        >
                            <div>
                                <div className="font-semibold">
                                    Épisode {ep.number}
                                </div>
                                {ep.title && (
                                    <div className="text-sm text-muted-foreground">
                                        {ep.title}
                                    </div>
                                )}
                            </div>

                            <div className="flex gap-2">
                               
                                <Link href={`/media/${media.id}/seasons/${season.id}/episodes/${ep.id}/edit`}>
                                    <Button variant="outline" size="sm">Modifier</Button>
                                </Link>

                                <Button
                                    variant="destructive"
                                    size="sm"
                                    onClick={() => handleDestroy(ep.id)}
                                >
                                    Supprimer
                                </Button>
                            </div>
                        </div>
                    ))
                )}
            </div>
        </AppLayout>
    );
}