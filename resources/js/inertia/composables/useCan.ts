import { usePage } from '@inertiajs/vue3';

type PageAuth = {
    auth: {
        permissions: string[];
    };
};

export const useCan = () => {
    const permissions = usePage<PageAuth>().props.auth?.permissions ?? [];

    const can = (permission: string): boolean => permissions.includes(permission);

    const canAny = (permissionList: string[]): boolean => permissionList.some((name) => can(name));

    return { can, canAny };
};
