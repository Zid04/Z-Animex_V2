import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';


import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { CharacterCounter } from '@/components/character-counter';
type Tag = { id: number; name: string };
type Props = { all_tags: Tag[] };

export default function MediaCreate({ all_tags }: Props) {
    const [titleLength, setTitleLength] = useState(0);
    const [descriptionLength, setDescriptionLength] = useState(0);
    const [coverPreview, setCoverPreview] = useState<string | null>(null);

    const MAX_TITLE = 255;
    const MAX_DESC = 5000;

    const { data, setData, post, processing, errors } = useForm({
        type: 'anime' as 'anime' | 'movie' | 'series',
        title: '',
        description: '',
        cover: '',
        status: 'Finished Airing',
        airing: false,
        is_public: true,
        year: '',
        studios: '',
        genres: [] as number[],
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post('/media');
    }

    function toggleGenre(id: number) {
        setData(
            'genres',
            data.genres.includes(id)
                ? data.genres.filter((g) => g !== id)
                : [...data.genres, id],
        );
    }

    return (
        <AppLayout>
            <Head title="Ajouter un média" />

            <div className="mx-auto max-w-2xl space-y-6">
                <h1 className="text-2xl font-semibold">Ajouter un média</h1>

                <form onSubmit={submit} className="space-y-6">
                    {/* Informations générales */}
                    <div className="space-y-4 border-b pb-6">
                        <h2 className="text-lg font-semibold">
                            Informations générales
                        </h2>

                        <div className="space-y-2">
                            <Label htmlFor="title">Titre *</Label>
                            <Input
                                id="title"
                                value={data.title}
                                maxLength={MAX_TITLE}
                                onChange={(e) => {
                                    setData('title', e.target.value);
                                    setTitleLength(e.target.value.length);
                                }}
                                required
                            />
                            <CharacterCounter
                                currentLength={titleLength}
                                maxLength={MAX_TITLE}
                                label="caractères"
                            />
                            <InputError message={errors.title} />
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="description">Description</Label>
                            <textarea
                                id="description"
                                value={data.description}
                                maxLength={MAX_DESC}
                                onChange={(e) => {
                                    setData('description', e.target.value);
                                    setDescriptionLength(e.target.value.length);
                                }}
                                className="min-h-[120px] w-full rounded border bg-background p-2 text-foreground"
                            />
                            <CharacterCounter
                                currentLength={descriptionLength}
                                maxLength={MAX_DESC}
                                label="caractères"
                            />
                            <InputError message={errors.description} />
                        </div>

                        {/* Cover */}
                        <div className="space-y-2">
                            <Label htmlFor="cover">
                                Image de couverture (URL)
                            </Label>
                            <Input
                                id="cover"
                                type="url"
                                placeholder="https://example.com/image.jpg"
                                value={data.cover}
                                onChange={(e) => {
                                    setData('cover', e.target.value);
                                    setCoverPreview(e.target.value || null);
                                }}
                            />
                            <InputError message={errors.cover} />
                            {coverPreview && (
                                <div className="mt-2 w-40 overflow-hidden rounded-lg border bg-muted p-2">
                                    <img
                                        src={coverPreview}
                                        alt="Aperçu"
                                        className="w-full rounded object-cover"
                                        onError={() => setCoverPreview(null)}
                                    />
                                </div>
                            )}
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div className="space-y-2">
                                <Label htmlFor="year">Année</Label>
                                <Input
                                    id="year"
                                    type="number"
                                    min="1900"
                                    max="2100"
                                    value={data.year}
                                    onChange={(e) =>
                                        setData('year', e.target.value)
                                    }
                                />
                                <InputError message={errors.year} />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="type">Type</Label>
                                <select
                                    id="type"
                                    value={data.type}
                                    onChange={(e) =>
                                        setData(
                                            'type',
                                            e.target.value as
                                                | 'anime'
                                                | 'movie'
                                                | 'series',
                                        )
                                    }
                                    className="w-full rounded border bg-background p-2 text-foreground"
                                >
                                    <option value="anime">Anime</option>
                                    <option value="movie">Film</option>
                                    <option value="series">Série</option>
                                </select>
                                <InputError message={errors.type} />
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div className="space-y-2">
                                <Label htmlFor="status">Statut</Label>
                                <select
                                    id="status"
                                    value={data.status}
                                    onChange={(e) =>
                                        setData('status', e.target.value)
                                    }
                                    className="w-full rounded border bg-background p-2 text-foreground"
                                >
                                    <option value="Finished Airing">
                                        Terminé
                                    </option>
                                    <option value="Currently Airing">
                                        En cours de diffusion
                                    </option>
                                    <option value="Not yet aired">
                                        À venir
                                    </option>
                                </select>
                                <InputError message={errors.status} />
                            </div>

                            <div className="space-y-2">
                                <Label>En cours de diffusion</Label>
                                <div className="flex h-10 items-center gap-3">
                                    <button
                                        type="button"
                                        onClick={() =>
                                            setData('airing', !data.airing)
                                        }
                                        className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${
                                            data.airing
                                                ? 'bg-primary'
                                                : 'bg-muted'
                                        }`}
                                    >
                                        <span
                                            className={`inline-block h-4 w-4 transform rounded-full bg-white transition-transform ${
                                                data.airing
                                                    ? 'translate-x-6'
                                                    : 'translate-x-1'
                                            }`}
                                        />
                                    </button>
                                    <span className="text-sm text-muted-foreground">
                                        {data.airing ? 'Oui' : 'Non'}
                                    </span>
                                </div>
                                <InputError message={errors.airing} />
                            </div>
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="is_public">Visibilité</Label>
                            <select
                                id="is_public"
                                value={data.is_public ? 'public' : 'private'}
                                onChange={(e) =>
                                    setData(
                                        'is_public',
                                        e.target.value === 'public',
                                    )
                                }
                                className="w-full rounded border bg-background p-2 text-foreground"
                            >
                                <option value="public">Public</option>
                                <option value="private">Privé</option>
                            </select>
                            <p className="text-xs text-muted-foreground">
                                Un média privé est visible seulement par son
                                propriétaire.
                            </p>
                            <InputError message={errors.is_public} />
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="studios">Studios</Label>
                            <Input
                                id="studios"
                                placeholder="ex: MAPPA, Bones"
                                value={data.studios}
                                onChange={(e) =>
                                    setData('studios', e.target.value)
                                }
                            />
                            <p className="text-xs text-muted-foreground">
                                Séparez les studios par une virgule
                            </p>
                            <InputError message={errors.studios} />
                        </div>
                    </div>

                    {/* Genres */}
                    <div className="space-y-4">
                        <h2 className="text-lg font-semibold">Genres / Tags</h2>
                        <div className="flex flex-wrap gap-2">
                            {all_tags.map((tag) => (
                                <button
                                    key={tag.id}
                                    type="button"
                                    onClick={() => toggleGenre(tag.id)}
                                    className={`rounded-full border px-3 py-1 text-sm transition ${
                                        data.genres.includes(tag.id)
                                            ? 'bg-primary text-primary-foreground'
                                            : 'bg-muted hover:bg-secondary'
                                    }`}
                                >
                                    {tag.name}
                                </button>
                            ))}
                        </div>
                        <InputError message={errors.genres} />
                    </div>

                    <Button
                        type="submit"
                        disabled={processing}
                        className="w-full"
                    >
                        {processing ? 'Envoi…' : 'Créer'}
                    </Button>
                </form>
            </div>
        </AppLayout>
    );
}
