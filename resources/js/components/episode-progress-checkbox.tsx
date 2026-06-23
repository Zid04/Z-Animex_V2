import { Form } from '@inertiajs/react';
import { useRef } from 'react';
import { Checkbox } from '@/components/ui/checkbox';

interface EpisodeProgressCheckboxProps {
    mediaId: number;
    seasonId: number;
    episodeId: number;
    watched: boolean;
    storeUrl: string;
    destroyUrl: string;
}

export function EpisodeProgressCheckbox({
    watched,
    storeUrl,
    destroyUrl,
}: EpisodeProgressCheckboxProps) {
    const formRef = useRef<HTMLFormElement>(null);

    const handleCheckedChange = () => {
        // Soumettre le formulaire via la ref
        if (formRef.current) {
            formRef.current.submit();
        }
    };

    return (
        <Form
            ref={formRef}
            action={watched ? destroyUrl : storeUrl}
            method={watched ? 'DELETE' : 'POST'}
            className="inline-block"
        >
            {() => (
                <Checkbox
                    checked={watched}
                    onCheckedChange={handleCheckedChange}
                    className="cursor-pointer"
                />
            )}
        </Form>
    );
}
