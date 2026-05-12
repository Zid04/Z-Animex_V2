import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState } from 'react';

import { EpisodeProgressCheckbox } from '@/components/episode-progress-checkbox';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

import { apiDelete, apiPost } from '@/lib/api';

/*
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/

type Episode = { id: number; number: number; title: string; watched: boolean };
type Season = { id: number; number: number; episodes: Episode[] };
type Comment = {
    id: number;
    content: string;
    user: { id: number; name: string };
};
type Tag = { id: number; name: string };

const STATUS_LABELS: Record<string, string> = {
    watching: 'En cours',
    completed: 'Terminé',
    planned: 'Planifié',
    dropped: 'Abandonné',
};

const ALL_STATUSES = ['watching', 'completed', 'planned', 'dropped'] as const;

type Props = {
    media: {
        id: number;
        title: string;
        description?: string;
        type: string;
        year?: number;
        cover?: string | null;
        images?: { jpg?: { large_image_url?: string; image_url?: string } };
        score?: number;
        average_rating?: number | null;
        approved: boolean;
        studios?: Array<{ name: string }>;
        genres?: Array<{ name: string }>;
        tags: Tag[];
        seasons: Season[];
        comments: Comment[];
    };
    all_tags: Tag[];
    progress: {
        episodes_total: number;
        episodes_watched: number;
        percent: number;
    };
    user_rating?: number | null;
    is_favorite?: boolean;
    user_media?: { id: number; status: string; progress: number } | null;
    auth: { user: { id: number } };
};

/*
|--------------------------------------------------------------------------
| Component
|--------------------------------------------------------------------------
*/

export default function MediaShow({
    media,
    all_tags,
    progress,
    user_rating,
    is_favorite,
    user_media,
    auth,
}: Props) {
    if (!media || !media.id) {
        return (
            <>
                <Head title="Erreur - Média non trouvé" />
                <div className="flex items-center justify-center min-h-screen">
                    <div className="text-center">
                        <h1 className="text-2xl font-bold text-red-600 mb-4">Média non trouvé</h1>
                        <p className="text-muted-foreground mb-4">
                            Le média que vous recherchez n'existe pas ou l'ID est invalide.
                        </p>
                        <Link href="/media">
                            <Button>Retour au catalogue</Button>
                        </Link>
                    </div>
                </div>
            </>
        );
    }

    const [expandedSeason, setExpandedSeason] = useState<number | null>(null);
    const [showAddSeason, setShowAddSeason] = useState(false);
    const [showAddEpisode, setShowAddEpisode] = useState<number | null>(null);
    const tags = media.tags ?? [];
    const seasons = media.seasons ?? [];
    const comments = media.comments ?? [];

    const imageUrl =
        media.cover ??
        media.images?.jpg?.large_image_url ??
        media.images?.jpg?.image_url;

    const commentForm = useForm({ content: '' });
    const seasonForm = useForm({ number: '' });
    const ratingForm = useForm({ rating: user_rating ? String(user_rating) : '5' });
    const tagForm = useForm({ tag_id: '' });
    const watchlistForm = useForm({
        media_id: media.id,
        status: 'planned' as typeof ALL_STATUSES[number],
        progress: 0,
    });

    function submitComment(e: React.FormEvent) {
        e.preventDefault();
        commentForm.post(`/api/media/${media.id}/comment`, {
            onSuccess: () => commentForm.reset(),
        });
    }

    async function submitSeason(e: React.FormEvent) {
        e.preventDefault();
        await apiPost(`/api/media/${media.id}/seasons`, {
            number: seasonForm.data.number,
        });
        seasonForm.reset();
        setShowAddSeason(false);
        router.reload();
    }

    function submitRating(e: React.FormEvent) {
        e.preventDefault();
        const rating = ratingForm.data.rating;

        if (!rating || isNaN(Number(rating)) || Number(rating) < 1 || Number(rating) > 10) {
            alert('Veuillez entrer une note valide entre 1 et 10');

            return;
        }

        ratingForm.post(`/media/${media.id}/ratings`, {
            onError: (errors) => {
                console.error('Erreur lors de la soumission:', errors);
                alert('Erreur lors de l\'enregistrement de votre note');
            },
        });
    }

    function deleteRating() {
        router.delete(`/media/${media.id}/ratings`);
    }

    function deleteMedia() {
        if (!media || !media.id){
             return;
        }

        if (!confirm('Supprimer ce média ?')){
            return;
        }

        router.delete(`/api/media/${media.id}`, {
            onSuccess: () => router.visit('/media'),
            onError: (errors) => console.error('Delete error', errors),
        });
    }

    function toggleFavorite() {
        if (!media?.id) {
            return;
        }
        
        if (is_favorite) {
            router.delete(`/favorites/${media.id}`);
        } else {
            router.post(`/favorites/${media.id}`);
        }
    }

    function addToWatchlist() {
        watchlistForm.post('/watchlist', {
            onSuccess: () => router.reload(),
        });
    }

    function removeFromWatchlist() {
        if (!user_media?.id){
             return;
}

        if (!confirm('Retirer ce média de votre watchlist ?')) {
            return;
        }

        router.delete(`/watchlist/${user_media.id}`, {
            onSuccess: () => router.reload(),
        });
    }

    function submitTagAttach(e: React.FormEvent) {
        e.preventDefault();

        if (!media?.id){

             return;
        }

        tagForm.post(`/api/media/${media.id}/tags`, {
            onSuccess: () => tagForm.reset(),
        });
    }

    function detachTag(tagId: number) {
        if (!media?.id) { 
            return;
           }

        router.delete(`/api/media/${media.id}/tags/${tagId}`);
    }

    function deleteComment(commentId: number) {
        if (!media || !media.id){
            return;
        }

        if (!confirm('Supprimer ce commentaire ?')) {
            return;
        }

        router.delete(`/api/media/${media.id}/comment/${commentId}`, {
            onSuccess: () => console.log('Commentaire supprimé'),
            onError: (errors) => console.error('Erreur suppression commentaire', errors),
        });
    }

    return (
        <>
            <Head title={media.title} />

            <div className="mx-auto max-w-3xl space-y-10">
                {/* HEADER */}
                <div className="flex items-start justify-between gap-4">
                    <div>
                        <h1 className="text-3xl font-semibold">{media.title}</h1>
                        <p className="mt-1 text-sm text-muted-foreground">
                            {media.type}
                            {media.year && ` • ${media.year}`}
                            {media.score && ` • ★ ${media.score}`}
                        </p>
                    </div>
                    <div className="flex shrink-0 gap-2">
                        {media?.id && (
                            <Link href={`/media/${media.id}/edit`}>
                                <Button variant="outline" size="sm">Modifier</Button>
                            </Link>
                        )}
                        <Button variant="destructive" size="sm" onClick={deleteMedia}>
                            Supprimer
                        </Button>
                    </div>
                </div>

                {/* IMAGE */}
                {imageUrl && (
                    <img
                        src={imageUrl}
                        alt={media.title}
                        className="max-h-[400px] w-full rounded-lg object-cover"
                    />
                )}

                {/* INFOS */}
                <div className="space-y-2 text-sm text-muted-foreground">
                    {media.studios && media.studios.length > 0 && (
                        <p>
                            <strong>Studios :</strong>{' '}
                            {media.studios.map((s) => s.name).join(', ')}
                        </p>
                    )}
                    {media.genres && media.genres.length > 0 && (
                        <p>
                            <strong>Genres MAL :</strong>{' '}
                            {media.genres.map((g) => g.name).join(', ')}
                        </p>
                    )}
                    {media.description && (
                        <p className="mt-2 leading-relaxed whitespace-pre-line text-foreground">
                            {media.description}
                        </p>
                    )}
                </div>

                {/* TAGS */}
                <div className="space-y-3">
                    <h2 className="text-lg font-semibold">Tags</h2>
                    <div className="flex flex-wrap gap-2">
                        {tags.map((tag) => (
                            <button
                                key={tag.id}
                                onClick={() => detachTag(tag.id)}
                                className="rounded-full border bg-muted px-3 py-1 text-sm transition hover:bg-destructive hover:text-destructive-foreground"
                            >
                                {tag.name} ✕
                            </button>
                        ))}
                    </div>
                    <form onSubmit={submitTagAttach} className="flex gap-2">
                        <select
                            value={tagForm.data.tag_id}
                            onChange={(e) => tagForm.setData('tag_id', e.target.value)}
                            className="flex-1 rounded border bg-background p-2 text-foreground"
                        >
                            <option value="">Sélectionner un tag</option>
                            {all_tags.map((tag) => (
                                <option key={tag.id} value={tag.id}>{tag.name}</option>
                            ))}
                        </select>
                        <Button type="submit" disabled={tagForm.processing}>Ajouter</Button>
                    </form>
                </div>

                {/* FAVORIS + WATCHLIST */}
                <div className="flex flex-wrap gap-3">
                    <Button
                        variant={is_favorite ? 'destructive' : 'secondary'}
                        onClick={toggleFavorite}
                    >
                        {is_favorite ? 'Retirer des favoris' : 'Ajouter aux favoris'}
                    </Button>

                    {user_media ? (
                        <div className="flex items-center gap-2">
                            <span className="rounded-full bg-primary/10 px-3 py-1 text-sm font-medium text-primary">
                                Watchlist : {STATUS_LABELS[user_media.status] ?? user_media.status}
                            </span>
                            <Button variant="destructive" size="sm" onClick={removeFromWatchlist}>
                                Retirer
                            </Button>
                        </div>
                    ) : (
                        <div className="flex items-center gap-2">
                            <select
                                value={watchlistForm.data.status}
                                onChange={(e) =>
                                    watchlistForm.setData('status', e.target.value as typeof ALL_STATUSES[number])
                                }
                                className="rounded border bg-background p-2 text-sm text-foreground"
                            >
                                {ALL_STATUSES.map((s) => (
                                    <option key={s} value={s}>{STATUS_LABELS[s]}</option>
                                ))}
                            </select>
                            <Button
                                variant="secondary"
                                onClick={addToWatchlist}
                                disabled={watchlistForm.processing}
                            >
                                + Watchlist
                            </Button>
                        </div>
                    )}
                </div>

                {/* NOTE */}
                <div className="space-y-3">
                    <h2 className="text-lg font-semibold">Votre note</h2>
                    <form onSubmit={submitRating} className="flex items-center gap-3">
                        <Input
                            type="number"
                            min="1"
                            max="10"
                            value={ratingForm.data.rating}
                            onChange={(e) => ratingForm.setData('rating', e.target.value)}
                            className="w-20"
                        />
                        <Button type="submit" disabled={ratingForm.processing}>
                            Enregistrer
                        </Button>
                        {user_rating && (
                            <Button type="button" variant="destructive" onClick={deleteRating}>
                                Retirer
                            </Button>
                        )}
                    </form>
                    {media.average_rating != null && (
                        <p className="text-sm text-muted-foreground">
                            Note moyenne : {media.average_rating}/10
                        </p>
                    )}
                </div>

                {/* COMMENTAIRES */}
                <div className="space-y-4">
                    <h2 className="text-lg font-semibold">Commentaires</h2>
                    <form onSubmit={submitComment} className="space-y-3">
                        <textarea
                            value={commentForm.data.content}
                            onChange={(e) => commentForm.setData('content', e.target.value)}
                            className="w-full rounded border bg-background p-2 text-foreground"
                            placeholder="Écrire un commentaire..."
                            rows={3}
                        />
                        <Button type="submit" disabled={commentForm.processing}>
                            Publier
                        </Button>
                    </form>
                    <div className="space-y-3">
                        {comments.map((comment) => (
                            <div key={comment.id} className="space-y-1 rounded border p-3">
                                <p className="text-sm font-semibold">{comment.user.name}</p>
                                <p className="text-sm">{comment.content}</p>
                                {comment.user.id === auth.user.id && (
                                    <Button
                                        variant="destructive"
                                        size="sm"
                                        onClick={() => deleteComment(comment.id)}
                                    >
                                        Supprimer
                                    </Button>
                                )}
                            </div>
                        ))}
                    </div>
                </div>

                {/* PROGRESSION + SAISONS */}
                {(media.type === 'series' || media.type === 'anime') && (
                    <div className="space-y-6">
                        {progress.episodes_total > 0 && (
                            <div className="space-y-2">
                                <h2 className="text-lg font-semibold">Progression</h2>
                                <div className="flex items-center gap-3">
                                    <div className="h-3 flex-1 overflow-hidden rounded bg-muted">
                                        <div
                                            className="h-full bg-primary transition-all"
                                            style={{ width: `${progress.percent}%` }}
                                        />
                                    </div>
                                    <span className="text-sm font-semibold">
                                        {progress.episodes_watched}/{progress.episodes_total}
                                    </span>
                                </div>
                            </div>
                        )}

                        <div className="space-y-4">
                            <div className="flex items-center justify-between">
                                <h2 className="text-lg font-semibold">Saisons</h2>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={() => setShowAddSeason(!showAddSeason)}
                                >
                                    {showAddSeason ? 'Annuler' : '+ Saison'}
                                </Button>
                            </div>

                            {showAddSeason && (
                                <form onSubmit={submitSeason} className="space-y-3 rounded border p-4">
                                    <Input
                                        type="number"
                                        placeholder="Numéro de la saison"
                                        min="1"
                                        value={seasonForm.data.number}
                                        onChange={(e) => seasonForm.setData('number', e.target.value)}
                                        required
                                    />
                                    <Button type="submit">Créer</Button>
                                </form>
                            )}

                            <div className="space-y-3">
                                {seasons.length === 0 ? (
                                    <p className="text-muted-foreground">Aucune saison</p>
                                ) : (
                                    seasons.map((season) => (
                                        <SeasonBlock
                                            key={season.id}
                                            season={season}
                                            mediaId={media.id}
                                            expanded={expandedSeason === season.id}
                                            onToggle={() =>
                                                setExpandedSeason(
                                                    expandedSeason === season.id ? null : season.id,
                                                )
                                            }
                                            showAddEpisode={showAddEpisode === season.id}
                                            onToggleAddEpisode={() =>
                                                setShowAddEpisode(
                                                    showAddEpisode === season.id ? null : season.id,
                                                )
                                            }
                                        />
                                    ))
                                )}
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </>
    );
}

/*
|--------------------------------------------------------------------------
| SeasonBlock
|--------------------------------------------------------------------------
*/

function SeasonBlock({
    season,
    mediaId,
    expanded,
    onToggle,
    showAddEpisode,
    onToggleAddEpisode,
}: {
    season: Season;
    mediaId: number;
    expanded: boolean;
    onToggle: () => void;
    showAddEpisode: boolean;
    onToggleAddEpisode: () => void;
}) {
    const [number, setNumber] = useState('');
    const [title, setTitle] = useState('');

    async function submitEpisode(e: React.FormEvent) {
        e.preventDefault();
        await apiPost(`/api/media/${mediaId}/seasons/${season.id}/episodes`, {
            number,
            title,
        });
        setNumber('');
        setTitle('');
        onToggleAddEpisode();
        router.reload();
    }

    async function deleteEpisode(episodeId: number) {
        if (!confirm('Supprimer cet épisode ?')){ 
            return;
        }

        await apiDelete(`/api/episodes/${episodeId}`);
        router.reload();
    }

    return (
        <div className="rounded border">
            <button
                onClick={onToggle}
                className="flex w-full items-center justify-between p-4 text-left hover:bg-muted"
            >
                <span className="font-semibold">Saison {season.number}</span>
                <span className="text-sm text-muted-foreground">
                    {season.episodes.length} épisode{season.episodes.length > 1 ? 's' : ''}
                </span>
            </button>

            {expanded && (
                <div className="space-y-3 border-t bg-muted/30 p-4">
                    {showAddEpisode && (
                        <form
                            onSubmit={submitEpisode}
                            className="space-y-2 rounded border bg-background p-3"
                        >
                            <Input
                                type="number"
                                placeholder="Numéro"
                                min="1"
                                value={number}
                                onChange={(e) => setNumber(e.target.value)}
                                required
                            />
                            <Input
                                type="text"
                                placeholder="Titre (optionnel)"
                                value={title}
                                onChange={(e) => setTitle(e.target.value)}
                            />
                            <div className="flex gap-2">
                                <Button type="submit" size="sm">Créer</Button>
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    onClick={onToggleAddEpisode}
                                >
                                    Annuler
                                </Button>
                            </div>
                        </form>
                    )}

                    {season.episodes.map((episode) => (
                        <div
                            key={episode.id}
                            className="flex items-center justify-between rounded border bg-background p-3"
                        >
                            <div className="flex flex-1 items-center gap-3">
                                <EpisodeProgressCheckbox
                                    mediaId={mediaId}
                                    seasonId={season.id}
                                    episodeId={episode.id}
                                    watched={episode.watched}
                                    storeUrl={`/media/${mediaId}/seasons/${season.id}/episodes/${episode.id}/progress`}
                                    destroyUrl={`/media/${mediaId}/seasons/${season.id}/episodes/${episode.id}/progress`}
                                />
                                <span className={episode.watched ? 'text-muted-foreground line-through' : ''}>
                                    <strong>Ép. {episode.number}</strong>
                                    {episode.title && ` : ${episode.title}`}
                                </span>
                            </div>
                            <Button
                                variant="destructive"
                                size="sm"
                                onClick={() => deleteEpisode(episode.id)}
                            >
                                ✕
                            </Button>
                        </div>
                    ))}

                    {!showAddEpisode && (
                        <Button variant="secondary" size="sm" onClick={onToggleAddEpisode}>
                            + Ajouter un épisode
                        </Button>
                    )}
                </div>
            )}
        </div>
    );
}