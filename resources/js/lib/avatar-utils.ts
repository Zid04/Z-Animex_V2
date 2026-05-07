/**
 * Utility functions for handling avatar paths
 * Normalizes avatar paths to work correctly with storage
 */
export function getAvatarUrl(avatarPath: string | null | undefined): string | undefined {
    if (!avatarPath) return undefined;

    // Normalize leading slashes
    const normalized = avatarPath.replace(/^\/+/, '');

    // Default avatars
    if (normalized.startsWith('Images/Avatars/Default/')) {
        const filename = normalized.replace('Images/Avatars/Default/', '');
        return `/Images/Avatars/Default/${filename}`;
    }

    // Uploaded avatars
    return `/storage/${normalized}`;
}
