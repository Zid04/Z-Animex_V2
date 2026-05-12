import { Head, useForm } from '@inertiajs/react';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';


type Props = {
    season: { id: number; number: number };
};

export default function EpisodeCreate({ season }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        number: '',
        title: '',
        duration: '',
        video_url: '',
        season_id: season.id, 
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post('/api/episodes'); 
    }

    return (
        <>
            <Head title={`Ajouter un épisode — Saison ${season.number}`} />

            <div className="max-w-xl mx-auto space-y-6">
                <h1 className="text-2xl font-semibold">
                    Ajouter un épisode — Saison {season.number}
                </h1>

                <form onSubmit={submit} className="space-y-6">
                    <div className="space-y-2">
                        <Label htmlFor="number">Numéro d'épisode</Label>
                        <Input
                            id="number"
                            type="number"
                            min="1"
                            value={data.number}
                            onChange={e => setData('number', e.target.value)}
                            required
                        />
                        <InputError message={errors.number} />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="title">Titre</Label>
                        <Input
                            id="title"
                            value={data.title}
                            onChange={e => setData('title', e.target.value)}
                        />
                        <InputError message={errors.title} />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="duration">Durée</Label>
                        <Input
                            id="duration"
                            type="number"
                            min="1"
                            value={data.duration}
                            onChange={e => setData('duration', e.target.value)}
                        />
                        <InputError message={errors.duration} />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="video_url">URL vidéo</Label>
                        <Input
                            id="video_url"
                            type="url"
                            value={data.video_url}
                            onChange={e => setData('video_url', e.target.value)}
                        />
                        <InputError message={errors.video_url} />
                    </div>

                    <Button type="submit" disabled={processing} className="w-full">
                        {processing ? 'Création…' : "Créer l'épisode"}
                    </Button>
                </form>
            </div>
        </>
    );
}