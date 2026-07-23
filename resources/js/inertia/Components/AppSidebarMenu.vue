<script setup lang="ts">
/**
 * AppSidebarMenu
 * Inertia app links; visibility uses shared Spatie permission names.
 */
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutDashboard, Users, TrendingUp } from 'lucide-vue-next';
import { useCan } from '../composables/useCan';

defineProps({
    onNavigate: { type: Function as () => (() => void) | undefined, default: undefined },
});

const page = usePage();
const { canAny } = useCan();

const linkClass = (href: string) => {
    const active = page.url === href || page.url.startsWith(`${href}/`);

    return active
        ? 'flex w-full items-center gap-3 rounded-md bg-indigo-50 px-3 py-2.5 text-sm font-medium text-indigo-700'
        : 'flex w-full items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50 hover:text-gray-900';
};

const close = () => {
    if (typeof onNavigate === 'function') {
        onNavigate();
    }
};
</script>

<template>
    <div class="space-y-1">
        <Link href="/app/dashboard" :class="linkClass('/app/dashboard')" @click="close">
            <LayoutDashboard :size="18" class="shrink-0" />
            <span>Dashboard</span>
        </Link>
        <Link
            v-if="['super admin', 'company', 'Director'].includes((page.props.auth as any)?.user?.type)"
            href="/app/owner-dashboard"
            :class="linkClass('/app/owner-dashboard')"
            @click="close"
        >
            <TrendingUp :size="18" class="shrink-0" />
            <span>Owner Dashboard</span>
        </Link>
        <Link
            v-if="canAny(['users.r', 'manage user'])"
            href="/app/users"
            :class="linkClass('/app/users')"
            @click="close"
        >
            <Users :size="18" class="shrink-0" />
            <span>Users</span>
        </Link>
    </div>
</template>
