<script setup lang="ts">
/**
 * WeeklyAgentPerformance/Index
 * Purpose: Render the standalone Weekly Agent Performance Report page with filters.
 * Key Props:
 *   - initialFilters: Initial search period/center filter inputs.
 *   - centers: Center array for the filter selector.
 *   - weeklyPerformanceReport: Aggregated agent performance rows array.
 */
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import PeriodFilter from '../../Components/OwnerDashboard/PeriodFilter.vue';
import WeeklyAgentPerformanceSection from '../../Components/OwnerDashboard/WeeklyAgentPerformanceSection.vue';

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
    initialFilters: { type: Object, required: true },
    centers: { type: Array, default: () => [] },
    weeklyPerformanceReport: { type: Array as () => AgentPerformanceRow[], default: () => [] },
});

const loading = ref(false);

const handleFilterChange = (newFilters: any) => {
    loading.value = true;
    router.get('/app/weekly-performance-report', newFilters, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onFinish: () => {
            loading.value = false;
        }
    });
};
</script>

<template>
    <AppLayout title="Weekly Agent Performance Report">
        <Head title="Weekly Agent Performance Report" />

        <div class="space-y-6">
            <!-- Header section -->
            <div class="flex flex-col gap-1">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Weekly Agent Performance</h1>
                <p class="text-sm text-gray-500">
                    Detailed statistics of agents' transfers, approvals, rejections, and sales ratios.
                </p>
            </div>

            <!-- Filter Bar -->
            <PeriodFilter
                :centers="centers"
                :initial-period="initialFilters.period"
                :initial-start-date="initialFilters.start_date"
                :initial-end-date="initialFilters.end_date"
                :initial-center-id="initialFilters.center_id"
                @change="handleFilterChange"
            />

            <!-- Main report component -->
            <div class="relative min-h-[300px]">
                <!-- Loading Overlay -->
                <div
                    v-if="loading"
                    class="absolute inset-0 z-10 flex items-center justify-center bg-gray-50/60 rounded-xl"
                >
                    <div class="flex flex-col items-center gap-3">
                        <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-indigo-600"></div>
                        <span class="text-sm font-medium text-gray-500">Refreshing performance report...</span>
                    </div>
                </div>

                <div :class="{ 'opacity-50 pointer-events-none transition-opacity duration-200': loading }">
                    <WeeklyAgentPerformanceSection
                        :data="weeklyPerformanceReport"
                        :loading="loading"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
