<script setup lang="ts">
import axios from 'axios';
import { computed, ref } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';

const agentUser = ref('');
const stage = ref('csv');
const selectedDialer = ref(4);
const response = ref<null | any>(null);
const error = ref('');
const loading = ref(false);

const parsedRows = computed(() => {
    if (!response.value?.success || !Array.isArray(response.value.data?.data)) {
        return null;
    }

    return response.value.data.data;
});

const headers = computed(() => {
    if (!parsedRows.value || parsedRows.value.length === 0) {
        return [];
    }

    return Object.keys(parsedRows.value[0]);
});

const fetchDetails = async () => {
    error.value = '';
    response.value = null;

    const trimmedAgent = agentUser.value.trim();
    if (!trimmedAgent) {
        error.value = 'Please enter an agent user ID.';
        return;
    }

    loading.value = true;

    try {
        const result = await axios.get(`/app/vicidial/agent/${encodeURIComponent(trimmedAgent)}`, {
            params: {
                stage: stage.value,
                dialer: selectedDialer.value,
            },
        });

        response.value = result.data;
    } catch (err: any) {
        error.value = err.response?.data?.message || err.message || 'Failed to fetch agent details.';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <AppLayout title="VICIdial Non-Agent API Test">
        <div class="space-y-6">
            <div class="grid gap-4 lg:grid-cols-[2fr_1fr]">
                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Agent User</label>
                    <input
                        v-model="agentUser"
                        type="text"
                        placeholder="EMP0000124"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Response Stage</label>
                    <select
                        v-model="stage"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="json">JSON</option>
                        <option value="csv">CSV</option>
                    </select>
                    <label class="mt-4 block">
                        <span class="mb-2 block text-sm font-medium text-gray-700">Dialer</span>
                        <select
                            v-model="selectedDialer"
                            class="mt-2 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option :value="4">Dialer 4</option>
                            <option :value="3">Dialer 3</option>
                            <option :value="1">Dialer 1</option>
                        </select>
                    </label>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button
                    class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="loading"
                    @click="fetchDetails"
                >
                    {{ loading ? 'Fetching...' : 'Fetch Details' }}
                </button>
            </div>

            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <p class="mb-4 text-sm font-semibold text-gray-700">API Response</p>

                <div v-if="error" class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    {{ error }}
                </div>

                <div v-else-if="response">
                    <div v-if="parsedRows">
                        <div class="mb-4 rounded-lg bg-slate-50 p-4 text-sm text-slate-700">
                            <strong>Parsed CSV Result</strong>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm text-slate-700">
                                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th v-for="header in headers" :key="header" class="px-3 py-2 font-semibold">
                                            {{ header }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    <tr v-for="(row, index) in parsedRows" :key="index">
                                        <td v-for="header in headers" :key="header" class="whitespace-nowrap px-3 py-2">
                                            {{ row[header] }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-4 text-xs text-slate-500">
                            <p class="font-semibold">Raw response</p>
                            <pre class="whitespace-pre-wrap break-words text-xs">{{ response.data.raw }}</pre>
                        </div>
                    </div>
                    <div v-else>
                        <pre class="whitespace-pre-wrap break-words text-sm text-gray-800">
{{ JSON.stringify(response, null, 2) }}
                        </pre>
                    </div>
                </div>

                <div v-else class="rounded-md border border-dashed border-gray-300 p-6 text-sm text-gray-500">
                    Enter an agent user and click Fetch Details to test VICIdial.
                </div>
            </div>
        </div>
    </AppLayout>
</template>
