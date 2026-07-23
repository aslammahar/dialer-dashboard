<script setup lang="ts">
/**
 * DashboardV1
 * CRM dashboard widgets inside AppLayout.
 */
import { computed, ref } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';

defineProps({
    appName: { type: String, required: true },
});

type KpiRange = 'daily' | 'weekly' | 'monthly';

const funnelLabels = ['New Leads', 'Contacted', 'Quoted', 'Application Sent', 'Policy Issued'];
const funnelLevels = [76, 58, 41, 28, 18];
const salesBars = [20, 16, 24, 14, 30, 22, 27, 18, 28, 20];

const kpiByRange: Record<KpiRange, { leads: number; contacted: number; conversions: number; premium: string; conversionRate: string }> = {
    daily: { leads: 86, contacted: 62, conversions: 9, premium: '$3,420', conversionRate: '14.5%' },
    weekly: { leads: 512, contacted: 376, conversions: 58, premium: '$21,860', conversionRate: '15.4%' },
    monthly: { leads: 2140, contacted: 1602, conversions: 246, premium: '$92,440', conversionRate: '15.3%' },
};

const closerPerformance = [
    { name: 'A. Khan', sales: 18, premium: '$7,120' },
    { name: 'J. Ali', sales: 16, premium: '$6,540' },
    { name: 'S. Ahmed', sales: 14, premium: '$5,830' },
];

const selectedRange = ref<KpiRange>('weekly');
const activeKpi = computed(() => kpiByRange[selectedRange.value]);

const rangeButtonClass = (active: boolean) =>
    active
        ? 'rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm'
        : 'rounded-md bg-white px-3 py-1.5 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50';
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="mb-6 flex flex-wrap justify-end gap-2">
            <button type="button" :class="rangeButtonClass(selectedRange === 'daily')" @click="selectedRange = 'daily'">Daily</button>
            <button type="button" :class="rangeButtonClass(selectedRange === 'weekly')" @click="selectedRange = 'weekly'">Weekly</button>
            <button type="button" :class="rangeButtonClass(selectedRange === 'monthly')" @click="selectedRange = 'monthly'">Monthly</button>
        </div>

        <div class="grid grid-cols-4 gap-4 max-xl:grid-cols-2 max-sm:grid-cols-1">
            <div class="rounded-lg bg-white p-5 ring-1 ring-gray-200">
                <p class="text-sm font-medium text-gray-500">Leads</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ activeKpi.leads }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 ring-1 ring-gray-200">
                <p class="text-sm font-medium text-gray-500">Contacted</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ activeKpi.contacted }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 ring-1 ring-gray-200">
                <p class="text-sm font-medium text-gray-500">Policies sold</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ activeKpi.conversions }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 ring-1 ring-gray-200">
                <p class="text-sm font-medium text-gray-500">Total premium</p>
                <p class="mt-1 text-2xl font-semibold text-indigo-600">{{ activeKpi.premium }}</p>
            </div>
        </div>

        <section class="mt-6 grid grid-cols-2 gap-6 max-lg:grid-cols-1">
            <article class="rounded-lg bg-white ring-1 ring-gray-200">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-base font-semibold text-gray-900">Lead funnel</h2>
                </div>
                <div class="space-y-4 p-6">
                    <div v-for="(level, index) in funnelLevels" :key="level" class="space-y-1">
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-700">{{ funnelLabels[index] }}</span>
                            <span class="text-gray-500">{{ level }}%</span>
                        </div>
                        <div class="h-2 rounded-full bg-gray-200">
                            <span class="block h-2 rounded-full bg-indigo-600" :style="{ width: `${level}%` }" />
                        </div>
                    </div>
                </div>
            </article>

            <article class="rounded-lg bg-white ring-1 ring-gray-200">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-base font-semibold text-gray-900">FE policy conversion</h2>
                </div>
                <div class="flex justify-center p-6">
                    <div class="grid h-36 w-36 place-items-center rounded-full bg-[conic-gradient(#4f46e5_0_15%,#e5e7eb_15%)]">
                        <div class="grid h-24 w-24 place-items-center rounded-full bg-white text-2xl font-bold text-gray-900 ring-1 ring-gray-200">
                            {{ activeKpi.conversionRate }}
                        </div>
                    </div>
                </div>
            </article>

            <article class="rounded-lg bg-white ring-1 ring-gray-200">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-base font-semibold text-gray-900">Sales activity</h2>
                </div>
                <div class="flex h-32 items-end gap-1.5 overflow-x-auto p-6">
                    <span
                        v-for="(height, index) in salesBars"
                        :key="index"
                        class="w-3 shrink-0 rounded-t bg-indigo-400"
                        :style="{ height: `${height * 3}px` }"
                    />
                </div>
            </article>

            <article class="rounded-lg bg-white ring-1 ring-gray-200">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-base font-semibold text-gray-900">Top closers</h2>
                </div>
                <ul class="divide-y divide-gray-100 p-6">
                    <li
                        v-for="closer in closerPerformance"
                        :key="closer.name"
                        class="flex justify-between py-2 text-sm"
                    >
                        <span class="font-medium text-gray-700">{{ closer.name }} ({{ closer.sales }})</span>
                        <span class="font-semibold text-indigo-600">{{ closer.premium }}</span>
                    </li>
                </ul>
            </article>
        </section>
    </AppLayout>
</template>
