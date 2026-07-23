<script setup>
import { ref, computed } from 'vue';
import KpiCard from './KpiCard.vue';
import TrendChart from './TrendChart.vue';
import { DollarSign, Percent, BarChart3, TrendingUp, HelpCircle, Search, ArrowUpDown } from 'lucide-vue-next';

const props = defineProps({
    data: { type: Object, required: true },
    closerStats: { type: Array, default: () => [] },
    loading: { type: Boolean, default: false },
});

// Prepare data for the trend chart
const chartCategories = computed(() => {
    if (!props.data?.trend) return [];
    return props.data.trend.map(t => {
        const d = new Date(t.date);
        return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
    });
});

const chartSeries = computed(() => {
    if (!props.data?.trend) return [];
    return [
        {
            name: 'Submissions',
            data: props.data.trend.map(t => parseInt(t.submissions || 0))
        },
        {
            name: 'Approved Sales',
            data: props.data.trend.map(t => parseInt(t.sales || 0))
        }
    ];
});

// Status column definitions with labels and colors
const statusColumns = [
    { key: 'status_pending',          label: 'Pending',           bg: 'bg-yellow-50',  text: 'text-yellow-700' },
    { key: 'status_approved',         label: 'Approved',          bg: 'bg-green-50',   text: 'text-green-700' },
    { key: 'status_cancelled',        label: 'Cancelled',         bg: 'bg-red-50',     text: 'text-red-600' },
    { key: 'status_nsf',              label: 'NSF',               bg: 'bg-orange-50',  text: 'text-orange-700' },
    { key: 'status_dnc',              label: 'DNC',               bg: 'bg-rose-50',    text: 'text-rose-700' },
    { key: 'status_underwriting',     label: 'Underwriting',      bg: 'bg-blue-50',    text: 'text-blue-700' },
    { key: 'status_need_to_reach',    label: 'Need to Reach',     bg: 'bg-sky-50',     text: 'text-sky-700' },
    { key: 'status_rejected',         label: 'Rejected',          bg: 'bg-red-50',     text: 'text-red-700' },
    { key: 'status_funded',           label: 'Funded',            bg: 'bg-emerald-50', text: 'text-emerald-700' },
    { key: 'status_charged_backed',   label: 'Charged Back',      bg: 'bg-purple-50',  text: 'text-purple-700' },
    { key: 'status_potential_lapsed', label: 'Potential Lapsed',  bg: 'bg-amber-50',   text: 'text-amber-700' },
];

// Closer stats table — search and sort
const closerSearch = ref('');
const sortField = ref('status_approved');
const sortAsc = ref(false);

const toggleSort = (field) => {
    if (sortField.value === field) {
        sortAsc.value = !sortAsc.value;
    } else {
        sortField.value = field;
        sortAsc.value = false;
    }
};

const filteredClosers = computed(() => {
    let rows = [...(props.closerStats || [])];

    if (closerSearch.value.trim()) {
        const q = closerSearch.value.trim().toLowerCase();
        rows = rows.filter(r => (r.closer_name || '').toLowerCase().includes(q));
    }

    rows.sort((a, b) => {
        const valA = a[sortField.value] ?? 0;
        const valB = b[sortField.value] ?? 0;
        if (typeof valA === 'string') {
            return sortAsc.value ? valA.localeCompare(valB) : valB.localeCompare(valA);
        }
        return sortAsc.value ? valA - valB : valB - valA;
    });

    return rows;
});

// Totals row
const closerTotals = computed(() => {
    const rows = props.closerStats || [];
    const totals = {
        total_calls: rows.reduce((s, r) => s + (r.total_calls || 0), 0),
        total_premium: rows.reduce((s, r) => s + (r.total_premium || 0), 0),
    };
    statusColumns.forEach(col => {
        totals[col.key] = rows.reduce((s, r) => s + (r[col.key] || 0), 0);
    });
    return totals;
});
</script>

<template>
    <div class="space-y-6">
        <!-- Sales KPI Grid -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <KpiCard
                label="Approved Sales"
                :value="data.total_sales"
                :delta="data.sales_delta"
                :icon="TrendingUp"
                :loading="loading"
            />
            <KpiCard
                label="Total Premium"
                :value="data.total_premium"
                :delta="data.premium_delta"
                prefix="$"
                :icon="DollarSign"
                :loading="loading"
            />
            <KpiCard
                label="Conversion Rate"
                :value="data.conversion_rate"
                suffix="%"
                :delta="0"
                :icon="Percent"
                :loading="loading"
            />
            <KpiCard
                label="Total Submissions"
                :value="data.total_submissions"
                :delta="0"
                :icon="BarChart3"
                :loading="loading"
            />
        </div>

        <!-- Trend Chart — Full Width -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-lg font-bold text-gray-900">Sales &amp; Submissions Trend</h4>
                    <p class="text-xs text-gray-500">Daily breakdown of submissions vs closed sales</p>
                </div>
            </div>
            <div v-if="loading" class="h-[350px] w-full flex items-center justify-center bg-gray-50 rounded-lg animate-pulse">
                <span class="text-sm text-gray-400">Loading trend...</span>
            </div>
            <div v-else-if="!chartCategories.length" class="h-[350px] w-full flex items-center justify-center bg-gray-50 rounded-lg">
                <span class="text-sm text-gray-400">No trend data available for this range.</span>
            </div>
            <TrendChart
                v-else
                :categories="chartCategories"
                :series="chartSeries"
                :height="320"
                type="area"
            />
        </div>

        <!-- Closer Performance Table -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-6 pb-4">
                <div>
                    <h4 class="text-lg font-bold text-gray-900">Closer Performance</h4>
                    <p class="text-xs text-gray-500">Individual closer stats for the selected period</p>
                </div>
                <!-- Search -->
                <div class="relative w-full sm:w-64">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" :size="16" />
                    <input
                        v-model="closerSearch"
                        type="text"
                        placeholder="Search closer..."
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 py-2 pl-9 pr-3 text-sm text-gray-700 placeholder:text-gray-400 focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100 transition"
                    />
                </div>
            </div>

            <div v-if="loading" class="px-6 pb-6 space-y-3 animate-pulse">
                <div v-for="i in 6" :key="i" class="flex justify-between items-center py-3">
                    <div class="h-4 w-32 rounded bg-gray-200"></div>
                    <div class="h-4 w-16 rounded bg-gray-200"></div>
                    <div class="h-4 w-16 rounded bg-gray-200"></div>
                    <div class="h-4 w-16 rounded bg-gray-200"></div>
                </div>
            </div>

            <div v-else-if="!filteredClosers.length" class="px-6 pb-6">
                <div class="h-[200px] w-full flex items-center justify-center bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-400">No closer data found.</span>
                </div>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-left min-w-[1200px]">
                    <thead>
                        <tr class="border-y border-gray-100 bg-gray-50/60 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-4 py-3 sticky left-0 bg-gray-50/95 z-10 cursor-pointer select-none" @click="toggleSort('closer_name')">
                                <span class="inline-flex items-center gap-1">Closer <ArrowUpDown :size="11" /></span>
                            </th>
                            <th class="px-3 py-3 text-center cursor-pointer select-none" @click="toggleSort('total_calls')">
                                <span class="inline-flex items-center gap-1">Total <ArrowUpDown :size="11" /></span>
                            </th>
                            <th
                                v-for="col in statusColumns"
                                :key="col.key"
                                class="px-3 py-3 text-center cursor-pointer select-none whitespace-nowrap"
                                @click="toggleSort(col.key)"
                            >
                                <span class="inline-flex items-center gap-1">{{ col.label }} <ArrowUpDown :size="11" /></span>
                            </th>
                            <th class="px-3 py-3 text-right cursor-pointer select-none whitespace-nowrap" @click="toggleSort('total_premium')">
                                <span class="inline-flex items-center gap-1 justify-end">Premium <ArrowUpDown :size="11" /></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <tr v-for="closer in filteredClosers" :key="closer.closer_id" class="hover:bg-indigo-50/30 transition-colors">
                            <td class="px-4 py-2.5 font-medium text-gray-900 whitespace-nowrap sticky left-0 bg-white z-10">
                                {{ closer.closer_name }}
                            </td>
                            <td class="px-3 py-2.5 text-center text-gray-700 font-semibold">
                                {{ closer.total_calls }}
                            </td>
                            <td v-for="col in statusColumns" :key="col.key" class="px-3 py-2.5 text-center">
                                <span
                                    v-if="closer[col.key] > 0"
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-bold"
                                    :class="[col.bg, col.text]"
                                >
                                    {{ closer[col.key] }}
                                </span>
                                <span v-else class="text-gray-300 text-xs">0</span>
                            </td>
                            <td class="px-3 py-2.5 text-right text-indigo-600 font-bold whitespace-nowrap">
                                ${{ parseFloat(closer.total_premium || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}
                            </td>
                        </tr>
                    </tbody>
                    <!-- Totals Footer -->
                    <tfoot>
                        <tr class="border-t-2 border-gray-200 bg-gray-50 text-sm font-bold">
                            <td class="px-4 py-3 text-gray-900 sticky left-0 bg-gray-50 z-10">Totals</td>
                            <td class="px-3 py-3 text-center text-gray-900">{{ closerTotals.total_calls }}</td>
                            <td v-for="col in statusColumns" :key="col.key" class="px-3 py-3 text-center" :class="col.text">
                                {{ closerTotals[col.key] }}
                            </td>
                            <td class="px-3 py-3 text-right text-indigo-600">
                                ${{ closerTotals.total_premium.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Outsourcing Stats Banner -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-xl border border-indigo-100 bg-indigo-50/40 p-5">
            <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-lg bg-indigo-100 text-indigo-700">
                    <HelpCircle :size="20" />
                </div>
                <div>
                    <h5 class="text-sm font-bold text-gray-900">Outsourced Closers Overview</h5>
                    <p class="text-xs text-gray-500">Sales volume generated by external call centers during this period</p>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <div>
                    <span class="text-xs text-gray-400 block uppercase font-semibold">Sales</span>
                    <span class="text-lg font-extrabold text-gray-900">{{ data.outsourced?.sales || 0 }}</span>
                </div>
                <div class="h-8 w-px bg-indigo-200"></div>
                <div>
                    <span class="text-xs text-gray-400 block uppercase font-semibold">Premium</span>
                    <span class="text-lg font-extrabold text-indigo-600">${{ parseFloat(data.outsourced?.premium || 0).toLocaleString(undefined, {minimumFractionDigits: 2}) }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
