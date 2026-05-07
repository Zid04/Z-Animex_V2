import axios from 'axios';

// Récupère le token CSRF
const getToken = (): string | undefined => {

    // IMPORTANT : vérifier qu'on est côté navigateur
    if (typeof document === 'undefined') {
        return undefined;
    }

    // Chercher dans le meta tag
    const metaToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

    if (metaToken) {
        return metaToken;
    }

    // Chercher dans les cookies
    const cookieToken = (name: string): string | undefined => {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);

        if (parts.length === 2) {
            return parts.pop()?.split(';').shift();
        }

        return undefined;
    };

    return cookieToken('XSRF-TOKEN');
};

const token = getToken();

if (token) {
    // Headers globaux
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // Méthodes spécifiques
    axios.defaults.headers.post['X-CSRF-TOKEN'] = token;
    axios.defaults.headers.put['X-CSRF-TOKEN'] = token;
    axios.defaults.headers.patch['X-CSRF-TOKEN'] = token;
    axios.defaults.headers.delete['X-CSRF-TOKEN'] = token;
}

// Intercepteur
axios.interceptors.request.use(
    config => {

        const freshToken = getToken();

        if (freshToken) {
            config.headers['X-CSRF-TOKEN'] = freshToken;
            config.headers['X-Requested-With'] = 'XMLHttpRequest';
        }

        return config;
    },
    error => {
        return Promise.reject(error);
    }
);

export default axios;