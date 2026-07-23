<script setup lang="ts">
/**
 * ClosedCalls/Index
 * Purpose: Paginated list of closed calls with search filter and unlimited scroll (Load More).
 * Key Props:
 *   - closedCalls: Paginated closed calls object with data array and pagination metadata.
 *   - filters: Active search query filters.
 */
import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { Loader2, Search } from 'lucide-vue-next';
import AppLayout from '../../Layouts/AppLayout.vue';

type UserSummary = {
    id: number;
    name: string;
};

type ClosedCallRow = {
    id: number;
    created_at: string;
    closer_id: number | null;
    closername: string | null;
    status: string;
    customer_eligibility: string | null;
    clients_id: number | null;
    carrier: string | null;
    monthly_premium: string | number | null;
    closer: UserSummary | null;
    client: UserSummary | null;
};

type ClosedCallsPayload = {
    data: ClosedCallRow[];
    current_page: number;
    last_page: number;
    total: number;
};

const props = defineProps({
    closedCalls: { type: Object as () => ClosedCallsPayload, required: true },
    filters: { type: Object as () => { search: string }, required: true },
});

const search = ref(props.filters.search ?? '');
const listedClosedCalls = ref<ClosedCallRow[]>([...props.closedCalls.data]);
const currentPage = ref(props.closedCalls.current_page);
const lastPage = ref(props.closedCalls.last_page);
const total = ref(props.closedCalls.total);
const loadingMore = ref(false);
const searching = ref(false);

const hasMore = computed(() => currentPage.value < lastPage.value);

let searchDebounce: ReturnType<typeof setTimeout> | undefined;

const syncFromFirstPage = (payload: ClosedCallsPayload) => {
    listedClosedCalls.value = [...payload.data];
    currentPage.value = payload.current_page;
    lastPage.value = payload.last_page;
    total.value = payload.total;
};

const runSearch = () => {
    searching.value = true;
    router.get(
        '/app/closed-calls',
        { search: search.value, page: 1 },
        {
            preserveState: true,
            replace: true,
            only: ['closedCalls', 'filters'],
            onFinish: () => {
                searching.value = false;
            },
        },
    );
};

const loadMore = () => {
    if (!hasMore.value || loadingMore.value) {
        return;
    }

    loadingMore.value = true;

    router.get(
        '/app/closed-calls',
        { search: search.value, page: currentPage.value + 1 },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['closedCalls'],
            onSuccess: (page) => {
                const payload = page.props.closedCalls as ClosedCallsPayload;
                listedClosedCalls.value.push(...payload.data);
                currentPage.value = payload.current_page;
                lastPage.value = payload.last_page;
                total.value = payload.total;
            },
            onFinish: () => {
                loadingMore.value = false;
            },
        },
    );
};

const formatDateTime = (dateStr: string) => {
    if (!dateStr) return 'N/A';
    const d = new Date(dateStr);
    return d.toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatPremium = (val: string | number | null | undefined) => {
    if (val === null || val === undefined || val === '') return 'N/A';
    const num = typeof val === 'number' ? val : parseFloat(val);
    if (isNaN(num)) return val;
    return `$${num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
};

const getStatusBadgeClass = (status: string) => {
    const s = (status || '').toLowerCase().trim();
    if (s === 'approved' || s === 'funded') {
        return 'inline-flex items-center rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20';
    }
    if (s === 'pending' || s === 'underwriting' || s === 'need to reach' || s === 'dnf') {
        return 'inline-flex items-center rounded-md bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800 ring-1 ring-inset ring-amber-600/20';
    }
    if (s === 'rejected' || s === 'cancelled' || s === 'nsf' || s === 'dnc' || s === 'charged_backed' || s === 'chargedback') {
        return 'inline-flex items-center rounded-md bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700 ring-1 ring-inset ring-rose-600/10';
    }
    return 'inline-flex items-center rounded-md bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-600 ring-1 ring-inset ring-gray-500/10';
};

watch(
    () => props.closedCalls,
    (payload) => {
        if (payload.current_page === 1) {
            syncFromFirstPage(payload);
        }
    },
    { deep: true },
);

watch(search, () => {
    window.clearTimeout(searchDebounce);
    searchDebounce = window.setTimeout(runSearch, 350);
});
</script>

<template>
    <AppLayout title="Closed Calls">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Closed Calls</h1>
                <p class="mt-1 text-sm text-gray-500">
                    A list of all logged closed call records with status, client details, and premium values.
                </p>
            </div>
        </div>

        <!-- Toolbar / Filter -->
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative max-w-md flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <Search class="h-4 w-4 text-gray-400" />
                </div>
                <input
                    v-model="search"
                    type="search"
                    placeholder="Search by customer, status, closer, or carrier..."
                    class="w-full rounded-md border border-gray-300 py-2 pl-10 pr-3 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
            </div>
            <div v-if="searching" class="flex items-center gap-1.5 text-sm text-gray-500">
                <Loader2 class="h-4 w-4 animate-spin text-indigo-600" />
                <span>Searching...</span>
            </div>
        </div>

        <p class="mb-3 text-sm text-gray-500">
            Showing {{ listedClosedCalls.length }} of {{ total }} closed call(s)
        </p>

        <!-- Table Container -->
        <div class="overflow-hidden rounded-lg bg-white shadow ring-1 ring-black ring-opacity-5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Date & Time
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Closer
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Customer Eligibility
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Client
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Carrier
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Monthly Premium
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="call in listedClosedCalls" :key="call.id" class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                {{ formatDateTime(call.created_at) }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                {{ call.closer?.name ?? call.closername ?? 'N/A' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                <span :class="getStatusBadgeClass(call.status)">
                                    {{ call.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ call.customer_eligibility ?? 'N/A' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                {{ call.client?.name ?? 'N/A' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                {{ call.carrier ?? 'N/A' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                {{ formatPremium(call.monthly_premium) }}
                            </td>
                        </tr>
                        <tr v-if="listedClosedCalls.length === 0 && !searching">
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                No closed calls found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Infinite Scroll / Load More Action -->
        <div v-if="hasMore" class="mt-6 flex justify-center">
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-md bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="loadingMore"
                @click="loadMore"
            >
                <Loader2 v-if="loadingMore" class="h-4 w-4 animate-spin text-gray-600" />
                <span>{{ loadingMore ? 'Loading more...' : 'Load more' }}</span>
            </button>
        </div>
    </AppLayout>
</template>
