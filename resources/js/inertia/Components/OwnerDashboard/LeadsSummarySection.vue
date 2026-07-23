<script setup lang="ts">
/**
 * LeadsSummarySection
 * Purpose: Render the Leads Summary tab within the Owner Dashboard,
 *          providing separate statistics for WFH and Onsite users from avatar_leads.
 * Key Props:
 *   - data: Leads summary statistics object (totals, approved, rejected, breakdown).
 *   - loading: Page loading state.
 */
import { computed, ref } from 'vue';
import { FileText, CheckCircle2, XCircle, ArrowUpDown, Shield } from 'lucide-vue-next';

type BreakdownRow = {
    status: string;
    wfh: number;
    onsite: number;
    total: number;
};

type CardStats = {
    wfh: number;
    onsite: number;
    total: number;
};

type LeadsPayload = {
    breakdown: BreakdownRow[];
    totals: CardStats;
    approved: CardStats;
    rejected: CardStats;
};

const props = defineProps({
    data: { type: Object as () => LeadsPayload, required: true },
    loading: { type: Boolean, default: false },
});

// Sort state for the breakdown table
const sortField = ref<'status' | 'wfh' | 'onsite' | 'total'>('total');
const sortAsc = ref(false);

const toggleSort = (field: 'status' | 'wfh' | 'onsite' | 'total') => {
    if (sortField.value === field) {
        sortAsc.value = !sortAsc.value;
    } else {
        sortField.value = field;
        sortAsc.value = false;
    }
};

const calculatePercent = (val: number, total: number) => {
    if (!total) return 0;
    return Math.round((val / total) * 100);
};

const sortedBreakdown = computed(() => {
    const rows = [...(props.data?.breakdown || [])];
    rows.sort((a, b) => {
        const valA = a[sortField.value];
        const valB = b[sortField.value];
        if (typeof valA === 'string' && typeof valB === 'string') {
            return sortAsc.value ? valA.localeCompare(valB) : valB.localeCompare(valA);
        }
        return sortAsc.value ? (valA as number) - (valB as number) : (valB as number) - (valA as number);
    });
    return rows;
});

const getStatusBadgeClass = (status: string) => {
    const s = (status || '').toLowerCase().trim();
    if (s === 'approved') {
        return 'inline-flex items-center rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20';
    }
    if (s === 'rejected') {
        return 'inline-flex items-center rounded-md bg-rose-50 px-2 py-0.5 text-xs font-semibold text-rose-700 ring-1 ring-inset ring-rose-600/10';
    }
    if (s === 'pending' || s === 'on review') {
        return 'inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-800 ring-1 ring-inset ring-amber-600/20';
    }
    return 'inline-flex items-center rounded-md bg-gray-50 px-2 py-0.5 text-xs font-semibold text-gray-600 ring-1 ring-inset ring-gray-500/10';
};
</script>

<template>
    <div class="space-y-6">
        <!-- KPI Card Grid -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <!-- Total Leads Card -->
            <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition duration-200">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-gray-500">Total Leads</span>
                    <div class="p-2.5 rounded-lg bg-indigo-50 text-indigo-600">
                        <FileText :size="20" />
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-bold tracking-tight text-gray-900">
                        {{ data?.totals?.total?.toLocaleString() ?? 0 }}
                    </h3>
                    <div class="mt-4 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-indigo-500"></span>
                                Work From Home (WFH)
                            </span>
                            <span class="font-semibold text-gray-900">
                                {{ data?.totals?.wfh?.toLocaleString() ?? 0 }} ({{ calculatePercent(data?.totals?.wfh ?? 0, data?.totals?.total ?? 0) }}%)
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-sky-400"></span>
                                Onsite / Office
                            </span>
                            <span class="font-semibold text-gray-900">
                                {{ data?.totals?.onsite?.toLocaleString() ?? 0 }} ({{ calculatePercent(data?.totals?.onsite ?? 0, data?.totals?.total ?? 0) }}%)
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approved Leads Card -->
            <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition duration-200">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-gray-500">Approved Leads</span>
                    <div class="p-2.5 rounded-lg bg-emerald-50 text-emerald-600">
                        <CheckCircle2 :size="20" />
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-bold tracking-tight text-gray-900">
                        {{ data?.approved?.total?.toLocaleString() ?? 0 }}
                    </h3>
                    <div class="mt-4 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                Work From Home (WFH)
                            </span>
                            <span class="font-semibold text-gray-900">
                                {{ data?.approved?.wfh?.toLocaleString() ?? 0 }} ({{ calculatePercent(data?.approved?.wfh ?? 0, data?.approved?.total ?? 0) }}%)
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-300"></span>
                                Onsite / Office
                            </span>
                            <span class="font-semibold text-gray-900">
                                {{ data?.approved?.onsite?.toLocaleString() ?? 0 }} ({{ calculatePercent(data?.approved?.onsite ?? 0, data?.approved?.total ?? 0) }}%)
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rejected Leads Card -->
            <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition duration-200">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-gray-500">Rejected Leads</span>
                    <div class="p-2.5 rounded-lg bg-rose-50 text-rose-600">
                        <XCircle :size="20" />
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-bold tracking-tight text-gray-900">
                        {{ data?.rejected?.total?.toLocaleString() ?? 0 }}
                    </h3>
                    <div class="mt-4 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                                Work From Home (WFH)
                            </span>
                            <span class="font-semibold text-gray-900">
                                {{ data?.rejected?.wfh?.toLocaleString() ?? 0 }} ({{ calculatePercent(data?.rejected?.wfh ?? 0, data?.rejected?.total ?? 0) }}%)
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-rose-300"></span>
                                Onsite / Office
                            </span>
                            <span class="font-semibold text-gray-900">
                                {{ data?.rejected?.onsite?.toLocaleString() ?? 0 }} ({{ calculatePercent(data?.rejected?.onsite ?? 0, data?.rejected?.total ?? 0) }}%)
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breakdown Table -->
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                    <Shield :size="18" class="text-indigo-600" />
                    Leads QA Status Breakdown (WFH vs Onsite)
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th
                                scope="col"
                                class="cursor-pointer px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 hover:bg-gray-100 hover:text-gray-900"
                                @click="toggleSort('status')"
                            >
                                <div class="flex items-center gap-1">
                                    QA Status
                                    <ArrowUpDown :size="12" />
                                </div>
                            </th>
                            <th
                                scope="col"
                                class="cursor-pointer px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 hover:bg-gray-100 hover:text-gray-900"
                                @click="toggleSort('wfh')"
                            >
                                <div class="flex items-center justify-end gap-1">
                                    Work From Home (WFH)
                                    <ArrowUpDown :size="12" />
                                </div>
                            </th>
                            <th
                                scope="col"
                                class="cursor-pointer px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 hover:bg-gray-100 hover:text-gray-900"
                                @click="toggleSort('onsite')"
                            >
                                <div class="flex items-center justify-end gap-1">
                                    Onsite Agents
                                    <ArrowUpDown :size="12" />
                                </div>
                            </th>
                            <th
                                scope="col"
                                class="cursor-pointer px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 hover:bg-gray-100 hover:text-gray-900"
                                @click="toggleSort('total')"
                            >
                                <div class="flex items-center justify-end gap-1">
                                    Total Leads
                                    <ArrowUpDown :size="12" />
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Distribution (WFH / Onsite)
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="row in sortedBreakdown" :key="row.status" class="hover:bg-gray-50">
                            <!-- Status -->
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                <span :class="getStatusBadgeClass(row.status)">
                                    {{ row.status.toUpperCase() }}
                                </span>
                            </td>
                            <!-- WFH count -->
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm text-gray-600 font-medium">
                                {{ row.wfh.toLocaleString() }}
                                <span class="ml-1 text-[11px] text-gray-400">
                                    ({{ calculatePercent(row.wfh, row.total) }}%)
                                </span>
                            </td>
                            <!-- Onsite count -->
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm text-gray-600 font-medium">
                                {{ row.onsite.toLocaleString() }}
                                <span class="ml-1 text-[11px] text-gray-400">
                                    ({{ calculatePercent(row.onsite, row.total) }}%)
                                </span>
                            </td>
                            <!-- Total leads -->
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                {{ row.total.toLocaleString() }}
                            </td>
                            <!-- Distribution Ratio visual bar -->
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center">
                                    <div class="flex h-2.5 w-36 overflow-hidden rounded-full bg-gray-100">
                                        <div
                                            v-if="row.total > 0"
                                            :style="{ width: `${calculatePercent(row.wfh, row.total)}%` }"
                                            class="bg-indigo-500"
                                            title="Work From Home"
                                        ></div>
                                        <div
                                            v-if="row.total > 0"
                                            :style="{ width: `${calculatePercent(row.onsite, row.total)}%` }"
                                            class="bg-sky-400"
                                            title="Onsite / Office"
                                        ></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- Empty state -->
                        <tr v-if="sortedBreakdown.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                No lead data found for this period.
                            </td>
                        </tr>
                        <!-- Totals Row -->
                        <tr v-else class="bg-gray-50/50 font-semibold border-t-2 border-gray-200">
                            <td class="px-6 py-4 text-sm text-gray-950 font-bold">TOTALS</td>
                            <td class="px-6 py-4 text-right text-sm text-gray-950 font-bold">
                                {{ data?.totals?.wfh?.toLocaleString() ?? 0 }}
                                <span class="ml-1 text-[11px] text-gray-500">
                                    ({{ calculatePercent(props.data?.totals?.wfh ?? 0, props.data?.totals?.total ?? 0) }}%)
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-950 font-bold">
                                {{ data?.totals?.onsite?.toLocaleString() ?? 0 }}
                                <span class="ml-1 text-[11px] text-gray-500">
                                    ({{ calculatePercent(props.data?.totals?.onsite ?? 0, props.data?.totals?.total ?? 0) }}%)
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-950 font-extrabold">
                                {{ data?.totals?.total?.toLocaleString() ?? 0 }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center">
                                    <div class="flex h-3 w-36 overflow-hidden rounded-full bg-gray-200">
                                        <div
                                            :style="{ width: `${calculatePercent(props.data?.totals?.wfh ?? 0, props.data?.totals?.total ?? 0)}%` }"
                                            class="bg-indigo-600"
                                        ></div>
                                        <div
                                            :style="{ width: `${calculatePercent(props.data?.totals?.onsite ?? 0, props.data?.totals?.total ?? 0)}%` }"
                                            class="bg-sky-500"
                                        ></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
