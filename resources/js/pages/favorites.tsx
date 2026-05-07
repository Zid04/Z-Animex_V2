import { Head, Link } from '@inertiajs/react';
import { Heart } from 'lucide-react';

import AppLayout from '@/layouts/app-layout';

/*
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/

type Media = {
    id: number;
    title: string;
    type: string;
    year?: number;
    cover?: string | null;
};

type FavoriteItem = {
    id: number;
    media_id: number;
    media: Media;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Paginated<T> = {
    data: T[];
    links: PaginationLink[];
};

type Props = {
    favorites: Paginated<FavoriteItem>;
};

/*
|--------------------------------------------------------------------------
| Favorites Page
|--------------------------------------------------------------------------
*/

export default function FavoritesPage({ favorites }: Props) {
    const count = favorites.data?.length ?? 0;

    return (
        <AppLayout>
            <Head title="Mes favoris" />

            {/* HEADER */}
            <div className="flex items-center justify-between mb-6">
                <div className="flex items-center gap-2">
                    <Heart className="w-6 h-6 fill-red-500 text-red-500" />
                    <h1 className="text-2xl font-semibold">Mes favoris</h1>
                </div>

                <p className="text-sm text-muted-foreground">
                    {count} favori{count > 1 ? 's' : ''}
                </p>
            </div>

            {/* EMPTY STATE */}
            {count === 0 ? (
                <div className="flex flex-col items-center justify-center py-12">
                    <Heart className="w-16 h-16 text-muted-foreground mb-4 opacity-50" />

                    <p className="text-muted-foreground mb-4">
                        Vous n'avez pas encore de favoris
                    </p>

                    <Link href="/media">
                        <button className="px-4 py-2 bg-primary text-white rounded-lg hover:opacity-90">
                            Découvrir des médias
                        </button>
                    </Link>
                </div>
            ) : (
                <>
                    {/* GRID */}
                    <div className="grid gap-4 md:grid-cols-3 lg:grid-cols-4 mb-6">
                        {favorites.data.map((favorite) => {
                            const media = favorite.media;

                            const imageSrc = media.cover
                                ? media.cover.startsWith('http')
                                    ? media.cover
                                    : `/storage/${media.cover}`
                                : '/images/placeholder.png';

                            return (
                                <Link
                                    key={media.id}
                                    href={`/media/${media.id}`}
                                    className="group"
                                >
                                    <div className="border rounded-lg overflow-hidden hover:shadow transition bg-background">

                                        <img
                                            src={imageSrc}
                                            alt={media.title}
                                            className="h-48 w-full object-cover group-hover:opacity-90 transition"
                                        />

                                        <div className="p-3">
                                            <div className="text-sm uppercase text-muted-foreground">
                                                {media.type}
                                            </div>

                                            <div className="font-semibold group-hover:text-foreground">
                                                {media.title}
                                            </div>

                                            {media.year && (
                                                <div className="text-xs text-muted-foreground">
                                                    {media.year}
                                                </div>
                                            )}
                                        </div>

                                    </div>
                                </Link>
                            );
                        })}
                    </div>

                    {/* PAGINATION */}
                    <div className="flex justify-center gap-1">
                        {favorites.links?.map((link, i) => (
                            <Link
                                key={i}
                                href={link.url || '#'}
                                className={`px-3 py-2 rounded text-sm ${
                                    link.active
                                        ? 'bg-primary text-white'
                                        : link.url
                                            ? 'bg-muted hover:bg-secondary'
                                            : 'text-muted-foreground cursor-not-allowed'
                                }`}
                            >
                                {link.label.replace(/&laquo;|&raquo;/g, (m) =>
                                    m === '&laquo;' ? '‹' : '›'
                                )}
                            </Link>
                        ))}
                    </div>
                </>
            )}
        </AppLayout>
    );
}