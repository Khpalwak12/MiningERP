import { usePage } from '@inertiajs/react';

export default function usePermission() {
    const user = usePage().props.auth?.user;

    const roles = Array.isArray(user?.roles) ? user.roles : Object.values(user?.roles ?? {});
    const permissions = Array.isArray(user?.permissions) ? user.permissions : Object.values(user?.permissions ?? {});

    const can = (permission) => {
        if (!user) return false;
        if (roles.includes('Super Admin')) return true;
        return permissions.includes(permission);
    };

    return { can };
}
