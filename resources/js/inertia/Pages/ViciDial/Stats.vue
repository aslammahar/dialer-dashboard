<script setup lang="ts">
import axios from 'axios';
import { computed, ref } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';

const DEFAULT_DATE_RANGE_DAYS = 30;
const DIALER_OPTIONS = [
    { id: 4, label: 'Dialer 4' },
    { id: 3, label: 'Dialer 3' },
    { id: 1, label: 'Dialer 1' },
];

const agentRows = ref<Array<Record<string, string>> | null>(null);
const error = ref('');
const loading = ref(false);
const startDate = ref('');
const endDate = ref('');
const selectedDialer = ref(4);
const groupByCampaign = ref('YES');

const parseDuration = (value: string) => {
    if (!value) {
        return 0;
    }

    const parts = value.split(':').map((segment) => Number(segment) || 0);
    if (parts.length === 3) {
        return parts[0] * 3600 + parts[1] * 60 + parts[2];
    }

    if (parts.length === 2) {
        return parts[0] * 60 + parts[1];
    }

    return 0;
};

const formatDuration = (seconds: number) => {
    const sign = seconds < 0 ? '-' : '';
    const absSeconds = Math.abs(seconds);
    const hours = Math.floor(absSeconds / 3600);
    const minutes = Math.floor((absSeconds % 3600) / 60);
    const secs = absSeconds % 60;
    return `${sign}${hours}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
};

const summary = computed(() => {
    if (!agentRows.value || agentRows.value.length === 0) {
        return null;
    }

    const calls = agentRows.value.reduce((total, row) => total + Number(row.calls || '0'), 0);
    const waitSeconds = agentRows.value.reduce((total, row) => total + parseDuration(row.avg_wait_time || ''), 0);
    const sessionSeconds = agentRows.value.reduce((total, row) => total + parseDuration(row.avg_session || ''), 0);
    const talkSeconds = agentRows.value.reduce((total, row) => total + parseDuration(row.avg_talk_time || ''), 0);

    // Count unique users/agents to avoid over-counting if grouped by campaign
    const uniqueUsers = new Set(
        agentRows.value.map(row => row.user || row.user_id || row.agent || row.agent_user || '').filter(Boolean)
    );
    const agentsCount = uniqueUsers.size > 0 ? uniqueUsers.size : agentRows.value.length;

    return {
        agents: agentsCount,
        calls,
        avgWait: formatDuration(Math.round(waitSeconds / agentRows.value.length)),
        avgSession: formatDuration(Math.round(sessionSeconds / agentRows.value.length)),
        avgTalk: formatDuration(Math.round(talkSeconds / agentRows.value.length)),
    };
});

const headers = computed(() => {
    if (!agentRows.value || agentRows.value.length === 0) {
        return [];
    }

    return Object.keys(agentRows.value[0]);
});

const applyDefaultDates = () => {
    const end = new Date();
    const start = new Date(end);
    start.setDate(end.getDate() - DEFAULT_DATE_RANGE_DAYS + 1);
    startDate.value = start.toISOString().slice(0, 10);
    endDate.value = end.toISOString().slice(0, 10);
};

applyDefaultDates();

const fetchStats = async () => {
    error.value = '';
    agentRows.value = null;

    if (!startDate.value || !endDate.value) {
        error.value = 'Please select both start and end dates.';
        return;
    }

    loading.value = true;

    try {
        const response = await axios.get('/app/vicidial/stats/data', {
            params: {
                start_date: `${startDate.value} 00:00:00`,
                end_date: `${endDate.value} 23:59:59`,
                stage: 'csv',
                dialer: selectedDialer.value,
                group_by_campaign: groupByCampaign.value,
            },
        });

        if (response.data.success) {
            agentRows.value = response.data.data.data || [];
        } else {
            error.value = response.data.message || 'Failed to load VICIdial stats.';
        }
    } catch (err: any) {
        error.value = err.response?.data?.message || err.message || 'Failed to load VICIdial stats.';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <AppLayout title="VICIdial Agent Stats">
        <div class="space-y-6">
            <section class="grid gap-4 lg:grid-cols-[1.5fr_1fr]">
                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <h2 class="text-lg font-semibold text-slate-900">Agent stats export</h2>
                    <p class="mt-2 text-sm text-slate-500">Fetch VICIdial agent statistics using the CRM export API.</p>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Start date</span>
                            <input
                                v-model="startDate"
                                type="date"
                                class="mt-2 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">End date</span>
                            <input
                                v-model="endDate"
                                type="date"
                                class="mt-2 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Dialer</span>
                            <select
                                v-model="selectedDialer"
                                class="mt-2 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option v-for="option in DIALER_OPTIONS" :key="option.id" :value="option.id">
                                    {{ option.label }}
                                </option>
                            </select>
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Group by Campaign</span>
                            <select
                                v-model="groupByCampaign"
                                class="mt-2 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="YES">Yes</option>
                                <option value="NO">No</option>
                            </select>
                        </label>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="loading"
                            @click="fetchStats"
                        >
                            {{ loading ? 'Loading...' : 'Load stats' }}
                        </button>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <h3 class="text-base font-semibold text-slate-900">Summary</h3>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Agents</p>
                            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ summary?.agents ?? '-' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Total calls</p>
                            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ summary?.calls ?? '-' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Avg wait time</p>
                            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ summary?.avgWait ?? '-' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Avg session</p>
                            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ summary?.avgSession ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900">Agent performance table</h3>
                </div>

                <div v-if="error" class="mt-4 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    {{ error }}
                </div>

                <div v-else-if="agentRows && agentRows.length > 0" class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th v-for="column in headers" :key="column" class="px-3 py-2 font-semibold">
                                    {{ column.replace(/_/g, ' ') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <tr v-for="(row, rowIndex) in agentRows" :key="rowIndex">
                                <td v-for="column in headers" :key="column" class="whitespace-nowrap px-3 py-2">
                                    {{ row[column] }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else class="mt-4 rounded-lg border border-dashed border-slate-300 p-6 text-sm text-slate-500">
                    Load statistics to view the agent export table.
                </div>
            </section>
        </div>
    </AppLayout>
</template>
