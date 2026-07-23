<script setup>
import { computed, ref, watch } from 'vue';
import {
    BriefcaseBusiness,
    Ellipsis,
    HandCoins,
    LayoutDashboard,
    Menu,
    PhoneCall,
    Search,
    Settings,
    ShieldCheck,
    Ticket,
    UserCheck,
    Users,
    X,
} from 'lucide-vue-next';
import { useCan } from '../composables/useCan';

/*
 * Font reference:
 * Font: Lato
 * Author: tyPoland Lukasz Dziedzic
 * Url: http://www.fontsquirrel.com/fonts/lato
 */

defineProps({
    appName: { type: String, required: true },
});

const { canAny } = useCan();

const roles = [
    {
        id: 'director',
        label: 'Director',
        icon: ShieldCheck,
        pages: [
            { id: 'director-dashboard', label: 'Dashboard', icon: LayoutDashboard },
            { id: 'director-statistics', label: 'Statistics', icon: Users },
            { id: 'director-settings', label: 'Settings', icon: Settings },
        ],
    },
    {
        id: 'team-lead',
        label: 'Team Lead',
        icon: BriefcaseBusiness,
        pages: [
            { id: 'teamlead-dashboard', label: 'Dashboard', icon: LayoutDashboard },
            { id: 'teamlead-team', label: 'Team', icon: UserCheck },
            { id: 'teamlead-notifications', label: 'Notifications', icon: Ticket },
        ],
    },
    {
        id: 'agent',
        label: 'Agent',
        icon: PhoneCall,
        pages: [
            { id: 'agent-dashboard', label: 'Dashboard', icon: LayoutDashboard },
            { id: 'agent-inbox', label: 'Inbox', icon: PhoneCall },
            { id: 'agent-notifications', label: 'Notifications', icon: Ticket },
        ],
    },
    {
        id: 'closer',
        label: 'Closer',
        icon: HandCoins,
        pages: [
            { id: 'closer-dashboard', label: 'Dashboard', icon: LayoutDashboard },
            { id: 'closer-sales', label: 'Sales', icon: HandCoins },
            { id: 'closer-settings', label: 'Settings', icon: Settings },
        ],
    },
    {
        id: 'qa',
        label: 'Qa',
        icon: UserCheck,
        pages: [
            { id: 'qa-dashboard', label: 'Dashboard', icon: LayoutDashboard },
            { id: 'qa-audits', label: 'Audits', icon: ShieldCheck },
            { id: 'qa-settings', label: 'Settings', icon: Settings },
        ],
    },
    {
        id: 'dialer-team',
        label: 'Dialer team',
        icon: PhoneCall,
        pages: [
            { id: 'dialer-dashboard', label: 'Dashboard', icon: LayoutDashboard },
            { id: 'dialer-calls', label: 'Call Queue', icon: PhoneCall },
            { id: 'dialer-notifications', label: 'Notifications', icon: Ticket },
        ],
    },
];

const selectedRoleId = ref('director');
const activePageId = ref('director-dashboard');
const isSidebarOpen = ref(false);

const selectedRole = computed(() => roles.find((role) => role.id === selectedRoleId.value) ?? roles[0]);
const sidebarPages = computed(() => selectedRole.value.pages);
const activePage = computed(() => sidebarPages.value.find((page) => page.id === activePageId.value) ?? sidebarPages.value[0]);
const selectedRange = ref('weekly');

const kpiByRange = {
    daily: {
        leads: 86,
        contacted: 62,
        conversions: 9,
        premium: '$3,420',
        conversionRate: '14.5%',
    },
    weekly: {
        leads: 512,
        contacted: 376,
        conversions: 58,
        premium: '$21,860',
        conversionRate: '15.4%',
    },
    monthly: {
        leads: 2140,
        contacted: 1602,
        conversions: 246,
        premium: '$92,440',
        conversionRate: '15.3%',
    },
};

const closerPerformance = [
    { name: 'A. Khan', sales: 18, premium: '$7,120' },
    { name: 'J. Ali', sales: 16, premium: '$6,540' },
    { name: 'S. Ahmed', sales: 14, premium: '$5,830' },
];

const activeKpi = computed(() => kpiByRange[selectedRange.value]);

const changeRole = (roleId) => {
    selectedRoleId.value = roleId;
    isSidebarOpen.value = false;
};

const changePage = (pageId) => {
    activePageId.value = pageId;
    isSidebarOpen.value = false;
};

watch(selectedRoleId, () => {
    activePageId.value = selectedRole.value.pages[0].id;
});
</script>

<template>
    <div class="min-h-screen bg-[var(--crm-bg-primary)] text-[var(--crm-text-main)]">
        <div class="grid min-h-screen grid-cols-1 lg:grid-cols-[300px_1fr]">
            <aside class="hidden bg-[var(--crm-bg-sidebar)] px-6 py-8 text-[var(--crm-text-on-sidebar)] lg:flex lg:flex-col">
                <div class="mb-8 flex items-center gap-3">
                    <span class="h-3 w-3 rounded-sm bg-[var(--crm-accent)] shadow-[0_0_0_5px_rgba(255,159,28,0.24)]" />
                    <div>
                        <h2 class="text-xl font-bold text-[var(--crm-text-on-primary)]">CRM Panel</h2>
                        <p class="text-sm text-[var(--crm-text-on-sidebar)]/75">Advanced Workspace</p>
                    </div>
                </div>

                <nav class="space-y-2">
                    <button
                        v-for="page in sidebarPages"
                        :key="page.id"
                        type="button"
                        class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-xl font-semibold transition"
                        :class="page.id === activePageId ? 'bg-[var(--crm-active-soft)] text-[var(--crm-accent)]' : 'text-[var(--crm-text-on-sidebar)]/85 hover:bg-[var(--crm-hover-sidebar)] hover:text-[var(--crm-text-on-primary)]'"
                        @click="changePage(page.id)"
                    >
                        <component :is="page.icon" :size="22" />
                        <span>{{ page.label }}</span>
                    </button>
                </nav>

                <div class="mt-auto border-t border-[var(--crm-text-on-sidebar)]/20 pt-4">
                    <p class="mb-3 text-xs font-bold uppercase tracking-[0.18em] text-[var(--crm-text-on-sidebar)]/65">Switch Role</p>
                    <div class="space-y-2">
                        <button
                            v-for="role in roles"
                            :key="role.id"
                            type="button"
                            class="flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-left text-lg font-semibold transition"
                            :class="role.id === selectedRoleId ? 'bg-[var(--crm-active-soft)] text-[var(--crm-accent)]' : 'text-[var(--crm-text-on-sidebar)]/85 hover:bg-[var(--crm-hover-sidebar)] hover:text-[var(--crm-text-on-primary)]'"
                            @click="changeRole(role.id)"
                        >
                            <component :is="role.icon" :size="22" />
                            <span>{{ role.label }}</span>
                        </button>
                    </div>
                </div>
            </aside>

            <div
                v-if="isSidebarOpen"
                class="fixed inset-0 z-40 bg-black/50 lg:hidden"
                @click="isSidebarOpen = false"
            />

            <aside
                class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full bg-[var(--crm-bg-sidebar)] px-5 py-6 text-[var(--crm-text-on-sidebar)] transition-transform lg:hidden"
                :class="{ 'translate-x-0': isSidebarOpen }"
            >
                <div class="mb-6 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-[var(--crm-text-on-primary)]">Navigation</h3>
                    <button type="button" class="rounded-lg bg-[var(--crm-hover-sidebar)] p-2 text-[var(--crm-text-on-primary)]" @click="isSidebarOpen = false">
                        <X :size="18" />
                    </button>
                </div>

                <nav class="space-y-2">
                    <button
                        v-for="page in sidebarPages"
                        :key="`m-${page.id}`"
                        type="button"
                        class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-xl font-semibold transition"
                        :class="page.id === activePageId ? 'bg-[var(--crm-active-soft)] text-[var(--crm-accent)]' : 'text-[var(--crm-text-on-sidebar)]/85 hover:bg-[var(--crm-hover-sidebar)] hover:text-[var(--crm-text-on-primary)]'"
                        @click="changePage(page.id)"
                    >
                        <component :is="page.icon" :size="22" />
                        <span>{{ page.label }}</span>
                    </button>
                </nav>

                <div class="mt-6 border-t border-[var(--crm-text-on-sidebar)]/20 pt-4">
                    <p class="mb-3 text-xs font-bold uppercase tracking-[0.18em] text-[var(--crm-text-on-sidebar)]/65">Switch Role</p>
                    <div class="space-y-2">
                        <button
                            v-for="role in roles"
                            :key="`r-${role.id}`"
                            type="button"
                            class="flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-left text-lg font-semibold transition"
                            :class="role.id === selectedRoleId ? 'bg-[var(--crm-active-soft)] text-[var(--crm-accent)]' : 'text-[var(--crm-text-on-sidebar)]/85 hover:bg-[var(--crm-hover-sidebar)] hover:text-[var(--crm-text-on-primary)]'"
                            @click="changeRole(role.id)"
                        >
                            <component :is="role.icon" :size="22" />
                            <span>{{ role.label }}</span>
                        </button>
                    </div>
                </div>
            </aside>

            <main class="bg-[var(--crm-bg-content)] p-5 md:p-8">
                <header class="mb-6 flex items-center justify-between gap-3">
                    <button
                        type="button"
                        class="rounded-xl bg-[var(--crm-bg-card-dark)] p-2.5 text-[var(--crm-text-on-primary)]"
                        @click="isSidebarOpen = true"
                    >
                        <Menu :size="20" />
                    </button>

                    <div class="flex w-full max-w-md items-center gap-2 rounded-xl bg-[var(--crm-bg-card-dark)] px-4 py-3 text-[var(--crm-text-on-sidebar)]">
                        <Search :size="18" />
                        <input
                            type="text"
                            placeholder="Search..."
                            class="w-full bg-transparent text-base text-[var(--crm-text-on-primary)] placeholder:text-[var(--crm-text-on-sidebar)]/60 focus:outline-none"
                        >
                    </div>

                    <button type="button" class="rounded-xl bg-[var(--crm-bg-card-dark)] p-2.5 text-[var(--crm-text-on-primary)]">
                        <Menu :size="20" />
                    </button>
                </header>

                <div class="mb-5 flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        class="rounded-lg px-4 py-2 text-sm font-bold uppercase tracking-wide transition"
                        :class="selectedRange === 'daily' ? 'bg-[var(--crm-accent)] text-[var(--crm-bg-card-dark)]' : 'bg-[var(--crm-bg-card-dark)] text-[var(--crm-text-on-primary)] hover:bg-[var(--crm-hover-sidebar)]'"
                        @click="selectedRange = 'daily'"
                    >
                        Daily
                    </button>
                    <button
                        type="button"
                        class="rounded-lg px-4 py-2 text-sm font-bold uppercase tracking-wide transition"
                        :class="selectedRange === 'weekly' ? 'bg-[var(--crm-accent)] text-[var(--crm-bg-card-dark)]' : 'bg-[var(--crm-bg-card-dark)] text-[var(--crm-text-on-primary)] hover:bg-[var(--crm-hover-sidebar)]'"
                        @click="selectedRange = 'weekly'"
                    >
                        Weekly
                    </button>
                    <button
                        type="button"
                        class="rounded-lg px-4 py-2 text-sm font-bold uppercase tracking-wide transition"
                        :class="selectedRange === 'monthly' ? 'bg-[var(--crm-accent)] text-[var(--crm-bg-card-dark)]' : 'bg-[var(--crm-bg-card-dark)] text-[var(--crm-text-on-primary)] hover:bg-[var(--crm-hover-sidebar)]'"
                        @click="selectedRange = 'monthly'"
                    >
                        Monthly
                    </button>
                </div>

                <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
                    <article class="rounded-2xl bg-[var(--crm-bg-card)] p-5 shadow-sm xl:row-span-2">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-2xl font-bold text-[var(--crm-text-main)]">Lead Funnel</h3>
                            <Ellipsis :size="18" class="text-[var(--crm-text-main)]" />
                        </div>
                        <div class="space-y-3">
                            <div v-for="level in [76, 58, 41, 28, 18]" :key="level" class="space-y-1">
                                <div class="h-2.5 rounded-full bg-[var(--crm-chart-track)]">
                                    <span class="block h-2.5 rounded-full bg-gradient-to-r from-[var(--crm-chart-1)] to-[var(--crm-chart-2)]" :style="{ width: `${level}%` }" />
                                </div>
                                <p class="text-sm font-semibold text-[var(--crm-text-muted)]">
                                    {{ ['New Leads', 'Contacted', 'Quoted', 'Application Sent', 'Policy Issued'][[76, 58, 41, 28, 18].indexOf(level)] }}
                                </p>
                            </div>
                        </div>
                    </article>

                    <article class="rounded-2xl bg-[var(--crm-bg-card)] p-5 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-2xl font-bold text-[var(--crm-text-main)]">FE Policy Conversion</h3>
                            <Ellipsis :size="18" class="text-[var(--crm-text-main)]" />
                        </div>
                        <div class="mx-auto grid h-40 w-40 place-items-center rounded-full bg-[conic-gradient(var(--crm-chart-1)_0_15%,var(--crm-chart-track)_15%)]">
                            <div class="grid h-28 w-28 place-items-center rounded-full bg-[var(--crm-bg-card)] text-3xl font-black text-[var(--crm-text-main)]">
                                {{ activeKpi.conversionRate }}
                            </div>
                        </div>
                        <p class="mt-3 text-center text-sm font-semibold text-[var(--crm-text-muted)]">
                            Policies issued vs contacted leads ({{ selectedRange }})
                        </p>
                    </article>

                    <article class="rounded-2xl bg-[var(--crm-bg-card-dark)] p-5 text-[var(--crm-text-on-card-dark)] shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-2xl font-bold text-[var(--crm-accent)]">Top Closers</h3>
                            <Ellipsis :size="18" />
                        </div>
                        <div class="space-y-2 text-base">
                            <div
                                v-for="closer in closerPerformance"
                                :key="closer.name"
                                class="flex items-center justify-between"
                            >
                                <span>{{ closer.name }} ({{ closer.sales }})</span>
                                <strong class="text-[var(--crm-accent)]">{{ closer.premium }}</strong>
                            </div>
                        </div>
                    </article>

                    <article class="rounded-2xl bg-[var(--crm-bg-card)] p-5 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-2xl font-bold text-[var(--crm-text-main)]">Sales Activity</h3>
                            <Ellipsis :size="18" class="text-[var(--crm-text-main)]" />
                        </div>
                        <div class="flex h-12 items-end gap-1.5">
                            <span v-for="h in [20, 16, 24, 14, 30, 22, 27, 18, 28, 20]" :key="h" class="w-3 rounded-t-md bg-[var(--crm-chart-2)]" :style="{ height: `${h}px` }" />
                        </div>
                    </article>

                    <article v-if="canAny(['view_dialer_stats'])" class="rounded-2xl bg-[var(--crm-bg-card)] p-5 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <h3 class="text-2xl font-bold text-[var(--crm-text-main)]">VICIdial Stats</h3>
                                <p class="text-sm text-[var(--crm-text-muted)]">Open the first statistics dashboard module for agent stats export.</p>
                            </div>
                            <Users :size="22" class="text-[var(--crm-accent)]" />
                        </div>
                        <a
                            href="/app/vicidial/stats"
                            class="inline-flex items-center gap-2 rounded-full bg-[var(--crm-accent)] px-4 py-2 text-sm font-semibold text-[var(--crm-bg-card-dark)] transition hover:bg-[var(--crm-accent)/90]"
                        >
                            Open VICIdial Stats
                        </a>
                    </article>

                    <article class="rounded-2xl bg-[var(--crm-bg-card-dark)] p-5 text-[var(--crm-text-on-card-dark)] shadow-sm xl:col-span-2">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-2xl font-bold">FE Insurance Sales Summary</h3>
                            <Ellipsis :size="18" />
                        </div>
                        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                            <div class="rounded-xl bg-white/5 p-3">
                                <p class="text-xs uppercase tracking-wide text-[var(--crm-text-on-sidebar)]/75">Leads</p>
                                <p class="text-3xl font-black text-[var(--crm-accent)]">{{ activeKpi.leads }}</p>
                            </div>
                            <div class="rounded-xl bg-white/5 p-3">
                                <p class="text-xs uppercase tracking-wide text-[var(--crm-text-on-sidebar)]/75">Contacted</p>
                                <p class="text-3xl font-black text-[var(--crm-accent)]">{{ activeKpi.contacted }}</p>
                            </div>
                            <div class="rounded-xl bg-white/5 p-3">
                                <p class="text-xs uppercase tracking-wide text-[var(--crm-text-on-sidebar)]/75">Policies Sold</p>
                                <p class="text-3xl font-black text-[var(--crm-accent)]">{{ activeKpi.conversions }}</p>
                            </div>
                            <div class="rounded-xl bg-white/5 p-3">
                                <p class="text-xs uppercase tracking-wide text-[var(--crm-text-on-sidebar)]/75">Total Premium</p>
                                <p class="text-3xl font-black text-[var(--crm-accent)]">{{ activeKpi.premium }}</p>
                            </div>
                        </div>
                        <p class="mt-4 max-w-3xl text-base text-[var(--crm-text-on-sidebar)]/85">
                            View by {{ selectedRange }} to monitor closer performance, lead quality, and final expense
                            policy conversion trend for call-center operations.
                        </p>
                    </article>
                </section>
            </main>
        </div>
    </div>
</template>
