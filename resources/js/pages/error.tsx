import { Head, Link } from '@inertiajs/react';
import { AlertCircle } from 'lucide-react';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';

type Props = {
    status: number;
    message: string;
};

export default function ErrorPage({ status, message }: Props) {
    const getErrorDetails = (code: number) => {
        const errors: Record<number, { title: string; description: string }> = {
            403: {
                title: 'Accès refusé',
                description: 'Vous n\'avez pas la permission d\'accéder à cette ressource. Seul le propriétaire peut effectuer cette action.',
            },
            404: {
                title: 'Page non trouvée',
                description: 'La ressource que vous recherchez n\'existe pas ou a été supprimée.',
            },
            500: {
                title: 'Erreur serveur',
                description: 'Une erreur inattendue s\'est produite. Veuillez réessayer plus tard.',
            },
        };

        return errors[code] || {
            title: `Erreur ${code}`,
            description: message || 'Une erreur s\'est produite.',
        };
    };

    const errorDetails = getErrorDetails(status);

    return (
        <AppLayout>
            <Head title={`${status} - ${errorDetails.title}`} />

            <div className="flex items-center justify-center min-h-[70vh]">
                <div className="text-center max-w-md">
                    <div className="flex justify-center mb-6">
                        <AlertCircle className="w-16 h-16 text-destructive" />
                    </div>

                    <h1 className="text-3xl font-bold mb-2">{status}</h1>
                    <h2 className="text-2xl font-semibold text-foreground mb-4">
                        {errorDetails.title}
                    </h2>

                    <p className="text-muted-foreground mb-8 leading-relaxed">
                        {errorDetails.description}
                    </p>

                    <div className="flex flex-col gap-3 sm:flex-row sm:justify-center">
                        <Link href="/dashboard">
                            <Button variant="outline">
                                Retour à l'accueil
                            </Button>
                        </Link>
                        <Link href="/media">
                            <Button>
                                Voir les médias
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
