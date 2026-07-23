<script setup lang="ts">
/**
 * WeeklyAgentPerformanceSection
 * Purpose: Render the Weekly Agent Performance Report inside the Owner Dashboard.
 * Key Props:
 *   - data: Array of raw agent performance statistics.
 *   - loading: Dashboard loading state.
 */
import { ref, computed } from 'vue';
import { Search, ArrowUpDown, TrendingUp, Calendar } from 'lucide-vue-next';

type AgentPerformanceRow = {
    agent_name: string;
    dialer_id: string;
    working_days: number;
    total_transfers: number;
    approved_transfers: number;
    rejected_transfers: number;
    sales: number;
};

const props = defineProps({
    data: { type: Array as () => AgentPerformanceRow[], default: () => [] },
    loading: { type: Boolean, default: false },
});

const searchQuery = ref('');
const sortField = ref<keyof AgentPerformanceRow | 'xpd' | 'ratio' | 'rej_pct' | 'app_pct'>('approved_transfers');
const sortAsc = ref(false);

const toggleSort = (field: keyof AgentPerformanceRow | 'xpd' | 'ratio' | 'rej_pct' | 'app_pct') => {
    if (sortField.value === field) {
        sortAsc.value = !sortAsc.value;
    } else {
        sortField.value = field;
        sortAsc.value = false;
    }
};

// Helper calculations
const getXPD = (row: AgentPerformanceRow) => {
    if (!row.working_days) return 0;
    return row.approved_transfers / row.working_days;
};

const getRatio = (row: AgentPerformanceRow) => {
    if (!row.sales) return 0;
    return row.approved_transfers / row.sales;
};

const getRejPct = (row: AgentPerformanceRow) => {
    if (!row.total_transfers) return 0;
    return (row.rejected_transfers / row.total_transfers) * 100;
};

const getAppPct = (row: AgentPerformanceRow) => {
    if (!row.total_transfers) return 0;
    return (row.approved_transfers / row.total_transfers) * 100;
};

// Calculated and filtered agents list
const processedAgents = computed(() => {
    return props.data.map(row => {
        return {
            ...row,
            xpd: getXPD(row),
            ratio: getRatio(row),
            rej_pct: getRejPct(row),
            app_pct: getAppPct(row)
        };
    });
});

const filteredAgents = computed(() => {
    let rows = [...processedAgents.value];

    if (searchQuery.value.trim()) {
        const q = searchQuery.value.trim().toLowerCase();
        rows = rows.filter(r => (r.agent_name || '').toLowerCase().includes(q));
    }

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

// SUM and AVG Row aggregates based on the CURRENT active/filtered rows
const aggregates = computed(() => {
    const rows = filteredAgents.value;
    const count = rows.length;

    if (count === 0) {
        return {
            sum: { working_days: 0, total_transfers: 0, approved_transfers: 0, rejected_transfers: 0, xpd: 0, sales: 0, ratio: 0, rej_pct: 0, app_pct: 0 },
            avg: { working_days: 0, total_transfers: 0, approved_transfers: 0, rejected_transfers: 0, xpd: 0, sales: 0, ratio: 0, rej_pct: 0, app_pct: 0 }
        };
    }

    // Sums
    const sumWorkingDays = rows.reduce((s, r) => s + r.working_days, 0);
    const sumTotalTransfers = rows.reduce((s, r) => s + r.total_transfers, 0);
    const sumApprovedTransfers = rows.reduce((s, r) => s + r.approved_transfers, 0);
    const sumRejectedTransfers = rows.reduce((s, r) => s + r.rejected_transfers, 0);
    const sumSales = rows.reduce((s, r) => s + r.sales, 0);
    const sumXpd = rows.reduce((s, r) => s + r.xpd, 0);

    // Ratios and percentages SUM matching spreadsheet logic
    const sumRatio = sumSales > 0 ? sumApprovedTransfers / sumSales : 0;
    const sumRejPct = rows.reduce((s, r) => s + r.rej_pct, 0) / count;
    const sumAppPct = rows.reduce((s, r) => s + r.app_pct, 0) / count;

    // Averages (straight average per agent)
    const avgWorkingDays = sumWorkingDays / count;
    const avgTotalTransfers = sumTotalTransfers / count;
    const avgApprovedTransfers = sumApprovedTransfers / count;
    const avgRejectedTransfers = sumRejectedTransfers / count;
    const avgSales = sumSales / count;
    const avgXpd = sumXpd / count;
    const avgRatio = rows.reduce((s, r) => s + r.ratio, 0) / count;
    const avgRejPct = sumRejPct; // percentage average is already computed
    const avgAppPct = sumAppPct;

    return {
        sum: {
            working_days: sumWorkingDays,
            total_transfers: sumTotalTransfers,
            approved_transfers: sumApprovedTransfers,
            rejected_transfers: sumRejectedTransfers,
            xpd: sumXpd,
            sales: sumSales,
            ratio: sumRatio,
            rej_pct: sumRejPct,
            app_pct: sumAppPct
        },
        avg: {
            working_days: avgWorkingDays,
            total_transfers: avgTotalTransfers,
            approved_transfers: avgApprovedTransfers,
            rejected_transfers: avgRejectedTransfers,
            xpd: avgXpd,
            sales: avgSales,
            ratio: avgRatio,
            rej_pct: avgRejPct,
            app_pct: avgAppPct
        }
    };
});
</script>

<template>
    <div class="space-y-6">
        <!-- Search bar -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative max-w-md flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <Search class="h-4 w-4 text-gray-400" />
                </div>
                <input
                    v-model="searchQuery"
                    type="search"
                    placeholder="Search agents by name..."
                    class="w-full rounded-md border border-gray-300 py-2 pl-10 pr-3 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
            </div>
            <div class="text-sm text-gray-500">
                Active Agents: {{ filteredAgents.length }}
            </div>
        </div>

        <!-- Summary Table -->
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                    <TrendingUp :size="18" class="text-indigo-600" />
                    Weekly Agent Performance Report
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <!-- Row Headers -->
                            <th scope="col" class="cursor-pointer px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-700 hover:bg-gray-200" @click="toggleSort('agent_name')">
                                <div class="flex items-center gap-1">Agent Names <ArrowUpDown :size="12" /></div>
                            </th>
                            <th scope="col" class="cursor-pointer px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-700 hover:bg-gray-200" @click="toggleSort('working_days')">
                                <div class="flex items-center justify-end gap-1">Working Days <ArrowUpDown :size="12" /></div>
                            </th>
                            <th scope="col" class="cursor-pointer px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-700 hover:bg-gray-200" @click="toggleSort('total_transfers')">
                                <div class="flex items-center justify-end gap-1">Total Xfers <ArrowUpDown :size="12" /></div>
                            </th>
                            <th scope="col" class="cursor-pointer px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-700 hover:bg-gray-200" @click="toggleSort('approved_transfers')">
                                <div class="flex items-center justify-end gap-1">App Xfers <ArrowUpDown :size="12" /></div>
                            </th>
                            <th scope="col" class="cursor-pointer px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-700 hover:bg-gray-200" @click="toggleSort('rejected_transfers')">
                                <div class="flex items-center justify-end gap-1">Rej Xfers <ArrowUpDown :size="12" /></div>
                            </th>
                            <th scope="col" class="cursor-pointer px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-700 hover:bg-gray-200" @click="toggleSort('xpd')">
                                <div class="flex items-center justify-end gap-1">X P D <ArrowUpDown :size="12" /></div>
                            </th>
                            <th scope="col" class="cursor-pointer px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-700 hover:bg-gray-200" @click="toggleSort('sales')">
                                <div class="flex items-center justify-end gap-1">Sales <ArrowUpDown :size="12" /></div>
                            </th>
                            <th scope="col" class="cursor-pointer px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-700 hover:bg-gray-200" @click="toggleSort('ratio')">
                                <div class="flex items-center justify-end gap-1">Xfers / Sales <ArrowUpDown :size="12" /></div>
                            </th>
                            <th scope="col" class="cursor-pointer px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-700 hover:bg-gray-200" @click="toggleSort('rej_pct')">
                                <div class="flex items-center justify-end gap-1">Rejection % <ArrowUpDown :size="12" /></div>
                            </th>
                            <th scope="col" class="cursor-pointer px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-700 hover:bg-gray-200" @click="toggleSort('app_pct')">
                                <div class="flex items-center justify-end gap-1">Approval % <ArrowUpDown :size="12" /></div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <!-- SUM Row at the Top (matching the spreadsheet screenshot layout) -->
                        <tr v-if="filteredAgents.length > 0" class="bg-gray-800 text-white font-semibold">
                            <td class="px-6 py-3 font-bold">SUM</td>
                            <td class="px-4 py-3 text-right">{{ aggregates.sum.working_days.toLocaleString() }}</td>
                            <td class="px-4 py-3 text-right">{{ aggregates.sum.total_transfers.toLocaleString() }}</td>
                            <td class="px-4 py-3 text-right">{{ aggregates.sum.approved_transfers.toLocaleString() }}</td>
                            <td class="px-4 py-3 text-right">{{ aggregates.sum.rejected_transfers.toLocaleString() }}</td>
                            <td class="px-4 py-3 text-right">{{ aggregates.sum.xpd.toFixed(1) }}</td>
                            <td class="px-4 py-3 text-right">{{ aggregates.sum.sales.toLocaleString() }}</td>
                            <td class="px-4 py-3 text-right">{{ aggregates.sum.ratio.toFixed(1) }}</td>
                            <td class="px-4 py-3 text-right">{{ aggregates.sum.rej_pct.toFixed(1) }}%</td>
                            <td class="px-6 py-3 text-right">{{ aggregates.sum.app_pct.toFixed(1) }}%</td>
                        </tr>

                        <!-- AVG Row at the Top (matching the spreadsheet screenshot layout) -->
                        <tr v-if="filteredAgents.length > 0" class="bg-gray-700 text-white font-semibold">
                            <td class="px-6 py-3 font-bold">AVG</td>
                            <td class="px-4 py-3 text-right">{{ aggregates.avg.working_days.toFixed(2) }}</td>
                            <td class="px-4 py-3 text-right">{{ aggregates.avg.total_transfers.toFixed(2) }}</td>
                            <td class="px-4 py-3 text-right">{{ aggregates.avg.approved_transfers.toFixed(2) }}</td>
                            <td class="px-4 py-3 text-right">{{ aggregates.avg.rejected_transfers.toFixed(2) }}</td>
                            <td class="px-4 py-3 text-right">{{ aggregates.avg.xpd.toFixed(2) }}</td>
                            <td class="px-4 py-3 text-right">{{ aggregates.avg.sales.toFixed(1) }}</td>
                            <td class="px-4 py-3 text-right">{{ aggregates.avg.ratio.toFixed(1) }}</td>
                            <td class="px-4 py-3 text-right"></td>
                            <td class="px-6 py-3 text-right"></td>
                        </tr>

                        <!-- Agent Rows -->
                        <tr v-for="row in filteredAgents" :key="row.dialer_id" class="hover:bg-gray-50/80">
                            <td class="whitespace-nowrap px-6 py-3.5 font-medium text-gray-900">
                                {{ row.agent_name }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-right text-gray-600 font-semibold bg-gray-50/30">
                                {{ row.working_days }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-right text-indigo-600 font-semibold bg-gray-50/30">
                                {{ row.total_transfers.toLocaleString() }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-right text-gray-600">
                                {{ row.approved_transfers.toLocaleString() }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-right text-gray-600">
                                {{ row.rejected_transfers.toLocaleString() }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-right text-gray-700 font-semibold bg-gray-50/50">
                                {{ row.xpd.toFixed(1) }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-right text-gray-900 font-bold">
                                {{ row.sales }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-right text-gray-600">
                                {{ row.ratio.toFixed(1) }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-right text-rose-600 font-medium">
                                {{ row.rej_pct.toFixed(1) }}%
                            </td>
                            <td class="whitespace-nowrap px-6 py-3.5 text-right text-emerald-600 font-semibold">
                                {{ row.app_pct.toFixed(1) }}%
                            </td>
                        </tr>

                        <!-- Empty state -->
                        <tr v-if="filteredAgents.length === 0">
                            <td colspan="10" class="px-6 py-12 text-center text-sm text-gray-500">
                                No agent performance data found for this period.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
