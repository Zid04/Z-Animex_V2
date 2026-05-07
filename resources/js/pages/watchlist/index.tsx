import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState } from 'react';

import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';

type MediaItem = {
    id: number;
    title: string;
    type: string;
    year?: number;
    images?: { jpg?: { image_url?: string } };
};

type UserMediaItem = {
    id: number;
    media_id: number;
    status: 'watching' | 'completed' | 'planned' | 'dropped';
    progress: number;
    started_at?: string | null;
    completed_at?: string | null;
    media: MediaItem;
};

type PaginationLink = { url: string | null; label: string; active: boolean };

type Props = {
    watchlist: {
        data: UserMediaItem[];
        meta?: { links?: PaginationLink[] };
    };
};

function paginationLabel(label: string) {
    return label
        .replace(/&laquo;/g, '‹')
        .replace(/&raquo;/g, '›')
        .replace(/&amp;/g, '&');
}

const STATUS_LABELS: Record<string, string> = {
    watching: 'En cours',
    completed: 'Terminé',
    planned: 'Planifié',
    dropped: 'Abandonné',
};

const STATUS_COLORS: Record<string, string> = {
    watching: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    planned: 'bg-yellow-100 text-yellow-800',
    dropped: 'bg-red-100 text-red-800',
};

const ALL_STATUSES = ['watching', 'completed', 'planned', 'dropped'] as const;

export default function WatchlistIndex({ watchlist }: Props) {
    const [activeStatus, setActiveStatus] = useState<string>('all');

    const filtered =
        activeStatus === 'all'
            ? watchlist.data
            : watchlist.data.filter((item) => item.status === activeStatus);

    const counts = watchlist.data.reduce(
        (acc, item) => {
            acc[item.status] = (acc[item.status] ?? 0) + 1;
            return acc;
        },
        {} as Record<string, number>,
    );

    return (
        <AppLayout>
            <Head title="Ma Watchlist" />

            <div className="mx-auto max-w-5xl space-y-8">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-semibold">Ma Watchlist</h1>
                    <p className="text-sm text-muted-foreground">
                        {watchlist.data.length} média
                        {watchlist.data.length > 1 ? 's' : ''}
                    </p>
                </div>

                {/* Filtres par statut */}
                <div className="flex flex-wrap gap-2">
                    <button
                        onClick={() => setActiveStatus('all')}
                        className={`rounded-full border px-4 py-2 text-sm font-medium transition ${
                            activeStatus === 'all'
                                ? 'border-primary bg-primary text-primary-foreground'
                                : 'border-transparent bg-muted hover:bg-secondary'
                        }`}
                    >
                        Tout ({watchlist.data.length})
                    </button>
                    {ALL_STATUSES.map((status) => (
                        <button
                            key={status}
                            onClick={() => setActiveStatus(status)}
                            className={`rounded-full border px-4 py-2 text-sm font-medium transition ${
                                activeStatus === status
                                    ? 'border-primary bg-primary text-primary-foreground'
                                    : 'border-transparent bg-muted hover:bg-secondary'
                            }`}
                        >
                            {STATUS_LABELS[status]} ({counts[status] ?? 0})
                        </button>
                    ))}
                </div>

                {/* Liste */}
                {filtered.length === 0 ? (
                    <div className="py-16 text-center text-muted-foreground">
                        Aucun média dans cette catégorie.
                    </div>
                ) : (
                    <div className="space-y-3">
                        {filtered.map((item) => (
                            <WatchlistCard key={item.id} item={item} />
                        ))}
                    </div>
                )}

                {/* Pagination */}
                {watchlist.meta?.links && (
                    <div className="flex flex-wrap justify-center gap-1">
                        {watchlist.meta.links.map((link, i) =>
                            link.url ? (
                                <Link
                                    key={i}
                                    href={link.url}
                                    className={`rounded border px-3 py-1 text-sm ${
                                        link.active
                                            ? 'bg-primary text-primary-foreground'
                                            : ''
                                    }`}
                                >
                                    {paginationLabel(link.label)}
                                </Link>
                            ) : (
                                <span
                                    key={i}
                                    className="cursor-not-allowed rounded border px-3 py-1 text-sm text-muted-foreground"
                                >
                                    {paginationLabel(link.label)}
                                </span>
                            ),
                        )}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}

function WatchlistCard({ item }: { item: UserMediaItem }) {
    const [editing, setEditing] = useState(false);

    const form = useForm({
        status: item.status,
        progress: item.progress,
    });

    const imageUrl = item.media.images?.jpg?.image_url;

    function submitUpdate(e: React.FormEvent) {
        e.preventDefault();
        form.patch(`/watchlist/${item.id}`, {
            onSuccess: () => setEditing(false),
        });
    }

    function handleDelete() {
        if (confirm(`Retirer "${item.media.title}" de votre watchlist ?`)) {
            router.delete(`/watchlist/${item.id}`);
        }
    }

    return (
        <div className="overflow-hidden rounded-lg border bg-background transition hover:shadow">
            <div className="flex gap-4 p-4">
                {/* Image */}
                <Link href={`/media/${item.media.id}`} className="shrink-0">
                    {imageUrl ? (
                        <img
                            src={imageUrl}
                            alt={item.media.title}
                            className="h-24 w-16 rounded object-cover"
                        />
                    ) : (
                        <div className="flex h-24 w-16 items-center justify-center rounded bg-muted">
                            <span className="text-xs text-muted-foreground">
                                —
                            </span>
                        </div>
                    )}
                </Link>

                {/* Infos */}
                <div className="min-w-0 flex-1 space-y-2">
                    <div className="flex items-start justify-between gap-2">
                        <div>
                            <Link
                                href={`/media/${item.media.id}`}
                                className="line-clamp-1 font-semibold hover:underline"
                            >
                                {item.media.title}
                            </Link>
                            <p className="text-xs text-muted-foreground">
                                {item.media.type}
                                {item.media.year && ` • ${item.media.year}`}
                            </p>
                        </div>
                        <span
                            className={`shrink-0 rounded-full px-2 py-1 text-xs ${STATUS_COLORS[item.status]}`}
                        >
                            {STATUS_LABELS[item.status]}
                        </span>
                    </div>

                    {item.progress > 0 && (
                        <div className="flex items-center gap-2">
                            <div className="h-1.5 flex-1 overflow-hidden rounded bg-muted">
                                <div
                                    className="h-full bg-primary transition-all"
                                    style={{
                                        width: `${Math.min(item.progress, 100)}%`,
                                    }}
                                />
                            </div>
                            <span className="shrink-0 text-xs text-muted-foreground">
                                {item.progress} ép.
                            </span>
                        </div>
                    )}

                    <div className="flex gap-2 pt-1">
                        <Button
                            variant="outline"
                            size="sm"
                            onClick={() => setEditing(!editing)}
                        >
                            {editing ? 'Annuler' : 'Modifier'}
                        </Button>
                        <Button
                            variant="destructive"
                            size="sm"
                            onClick={handleDelete}
                        >
                            Retirer
                        </Button>
                    </div>
                </div>
            </div>

            {/* Formulaire édition inline */}
            {editing && (
                <form
                    onSubmit={submitUpdate}
                    className="space-y-3 border-t bg-muted/30 p-4"
                >
                    <div className="grid grid-cols-2 gap-4">
                        <div className="space-y-1">
                            <label className="text-xs font-medium text-muted-foreground">
                                Statut
                            </label>
                            <select
                                value={form.data.status}
                                onChange={(e) =>
                                    form.setData(
                                        'status',
                                        e.target
                                            .value as typeof form.data.status,
                                    )
                                }
                                className="w-full rounded border bg-background p-2 text-sm text-foreground"
                            >
                                {ALL_STATUSES.map((s) => (
                                    <option key={s} value={s}>
                                        {STATUS_LABELS[s]}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="space-y-1">
                            <label className="text-xs font-medium text-muted-foreground">
                                Épisodes vus
                            </label>
                            <input
                                type="number"
                                min="0"
                                value={form.data.progress}
                                onChange={(e) =>
                                    form.setData(
                                        'progress',
                                        Number(e.target.value),
                                    )
                                }
                                className="w-full rounded border bg-background p-2 text-sm text-foreground"
                            />
                        </div>
                    </div>
                    <Button type="submit" size="sm" disabled={form.processing}>
                        {form.processing ? 'Enregistrement…' : 'Enregistrer'}
                    </Button>
                </form>
            )}
        </div>
    );
}
