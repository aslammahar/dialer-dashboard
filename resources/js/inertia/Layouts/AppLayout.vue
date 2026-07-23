<script setup lang="ts">
/**
 * AppLayout
 * App sidebar + main slot. Menu items use shared Spatie permissions.
 */
import { onUnmounted, ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutDashboard, Menu, Users, X, TrendingUp } from 'lucide-vue-next';
import { useCan } from '../composables/useCan';

defineProps({
    title: { type: String, required: true },
});

const page = usePage();
const { canAny } = useCan();

const appName = (page.props as { appName?: string }).appName ?? 'CRM';
const userName = page.props.auth?.user?.name ?? '';

const isSidebarOpen = ref(false);

const isActive = (href: string) => page.url === href || page.url.startsWith(`${href}/`);

const navLinkClass = (active: boolean) =>
    active
        ? 'flex w-full items-center gap-3 rounded-md bg-indigo-50 px-3 py-2.5 text-sm font-medium text-indigo-700'
        : 'flex w-full items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50 hover:text-gray-900';

watch(isSidebarOpen, (open) => {
    document.body.style.overflow = open ? 'hidden' : '';
});

onUnmounted(() => {
    document.body.style.overflow = '';
});
</script>

<template>
    <div class="min-h-screen bg-gray-100 lg:grid lg:grid-cols-[280px_minmax(0,1fr)]">
        <div
            v-if="isSidebarOpen"
            class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
            @click="isSidebarOpen = false"
        />

        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-[280px] -translate-x-full flex-col border-r border-gray-200 bg-white transition-transform duration-200 ease-out lg:static lg:z-auto lg:translate-x-0"
            :class="{ 'translate-x-0': isSidebarOpen }"
        >
            <div class="flex h-16 shrink-0 items-center justify-between border-b border-gray-200 px-5">
                <div class="min-w-0">
                    <p class="truncate text-base font-semibold text-gray-900">{{ appName }}</p>
                    <p class="truncate text-xs text-gray-500">{{ userName }}</p>
                </div>
                <button
                    type="button"
                    class="rounded-md p-1.5 text-gray-500 hover:bg-gray-100 lg:hidden"
                    aria-label="Close sidebar"
                    @click="isSidebarOpen = false"
                >
                    <X :size="18" />
                </button>
            </div>

            <nav class="flex-1 space-y-1 px-3 py-4">
                <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Menu</p>
                <Link
                    href="/app/dashboard"
                    :class="navLinkClass(isActive('/app/dashboard'))"
                    @click="isSidebarOpen = false"
                >
                    <LayoutDashboard :size="18" class="shrink-0" />
                    <span>Dashboard</span>
                </Link>
                <Link
                    v-if="['super admin', 'company', 'Director'].includes((page.props.auth as any)?.user?.type)"
                    href="/app/owner-dashboard"
                    :class="navLinkClass(isActive('/app/owner-dashboard'))"
                    @click="isSidebarOpen = false"
                >
                    <TrendingUp :size="18" class="shrink-0" />
                    <span>Owner Dashboard</span>
                </Link>
                <Link
                    v-if="canAny(['view performance report']) || ['super admin', 'company'].includes((page.props.auth as any)?.user?.type)"
                    href="/app/weekly-performance-report"
                    :class="navLinkClass(isActive('/app/weekly-performance-report'))"
                    @click="isSidebarOpen = false"
                >
                    <TrendingUp :size="18" class="shrink-0" />
                    <span>Weekly Performance Report</span>
                </Link>
                <Link
                    v-if="canAny(['users.r', 'manage user'])"
                    href="/app/users"
                    :class="navLinkClass(isActive('/app/users'))"
                    @click="isSidebarOpen = false"
                >
                    <Users :size="18" class="shrink-0" />
                    <span>Users</span>
                </Link>
                <Link
                    v-if="canAny(['view_dialer_stats'])"
                    href="/app/vicidial/stats"
                    :class="navLinkClass(isActive('/app/vicidial/stats'))"
                    @click="isSidebarOpen = false"
                >
                    <Users :size="18" class="shrink-0" />
                    <span>VICIdial Stats</span>
                </Link>
            </nav>
        </aside>

        <div class="flex min-h-screen min-w-0 flex-col">
            <div class="flex shrink-0 items-center gap-3 border-b border-gray-200 bg-white px-4 py-3 lg:hidden">
                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-md border border-gray-200 p-2 text-gray-600 hover:bg-gray-50"
                    aria-label="Open sidebar"
                    @click="isSidebarOpen = true"
                >
                    <Menu :size="20" />
                </button>
                <p class="truncate text-sm font-semibold text-gray-900">{{ title }}</p>
            </div>

            <main class="min-w-0 flex-1 overflow-x-hidden p-6 lg:p-8">
                <h1 class="mb-6 text-2xl font-semibold tracking-tight text-gray-900">{{ title }}</h1>
                <slot />
            </main>
        </div>
    </div>
</template>
