import { Head, Link } from '@inertiajs/react';

import { Button } from '@/components/ui/button';

type MediaItem = {
    id: number;
    title: string;
    type: string;
    year?: number;
    cover?: string | null;
    images?: { jpg?: { image_url?: string } };
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Props = {
    media: {
        data: MediaItem[];
        meta?: { links?: PaginationLink[] };
    };
};

function paginationLabel(label: string) {
    return label
        .replace(/&laquo;/g, '‹')
        .replace(/&raquo;/g, '›')
        .replace(/&amp;/g, '&');
}

export default function MyMedia({ media }: Props) {
    return (
        <>
            <Head title="Mes médias" />

            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-semibold">Mes médias</h1>
                <Button variant="outline" asChild>
                    <Link href="/my-media">Actualiser</Link>
                </Button>
            </div>

            {media.data.length === 0 && (
                <p className="text-muted-foreground">
                    Vous n'avez encore ajouté aucun média.
                </p>
            )}

            <div className="grid gap-4 md:grid-cols-3 lg:grid-cols-4">
                {media.data.map((item) => {
                    const img = item.cover ?? item.images?.jpg?.image_url;

                    return (
                        <div
                            key={item.id}
                            className="overflow-hidden rounded-lg border bg-background transition hover:shadow"
                        >
                            <Link href={`/media/${item.id}`} className="block">
                                {img && (
                                    <img
                                        src={img}
                                        alt={item.title}
                                        className="h-48 w-full object-cover"
                                    />
                                )}
                            </Link>
                            <div className="space-y-2 p-3">
                                <div className="text-xs text-muted-foreground uppercase">
                                    {item.type}
                                </div>
                                <div className="font-semibold">
                                    {item.title}
                                </div>
                                {item.year && (
                                    <div className="text-xs text-muted-foreground">
                                        {item.year}
                                    </div>
                                )}
                                <div className="flex gap-2 pt-2">
                                    <Button
                                        variant="secondary"
                                        className="flex-1"
                                        asChild
                                    >
                                        <Link href={`/media/${item.id}`}>
                                            Voir
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="outline"
                                        className="flex-1"
                                        asChild
                                    >
                                        <Link href={`/media/${item.id}/edit`}>
                                            Modifier
                                        </Link>
                                    </Button>
                                </div>
                            </div>
                        </div>
                    );
                })}
            </div>

            {media.meta?.links && (
                <div className="mt-6 flex flex-wrap justify-center gap-1">
                    {media.meta.links.map((link, i) =>
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
        </>
    );
}
