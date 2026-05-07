function csrfToken(): string {
    return (
        document.querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content') ?? ''
    );
}

async function apiRequest(
    url: string,
    method: string,
    data?: object | FormData
): Promise<unknown> {
    const isFormData = data instanceof FormData;

    const res = await fetch(url, {
        method,
        headers: isFormData
            ? { 'X-CSRF-TOKEN': csrfToken() }
            : {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': csrfToken(),
              },
        body: data
            ? isFormData
                ? data
                : JSON.stringify(data)
            : undefined,
    });

    if (!res.ok) {
        const text = await res.text();
        throw new Error(text || `HTTP ${res.status}`);
    }

    try {
        return await res.json();
    } catch {
        return null;
    }
}

export function apiPost(url: string, data?: object) {
    return apiRequest(url, 'POST', data);
}

export function apiDelete(url: string) {
    return apiRequest(url, 'DELETE');
}
