<script setup>
import { ref, watch, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';
import PeriodFilter from '../Components/OwnerDashboard/PeriodFilter.vue';
import SalesSummarySection from '../Components/OwnerDashboard/SalesSummarySection.vue';
import LeadsSummarySection from '../Components/OwnerDashboard/LeadsSummarySection.vue';
import ClosedCallsSection from '../Components/OwnerDashboard/ClosedCallsSection.vue';
import TeamLeaderboardSection from '../Components/OwnerDashboard/TeamLeaderboardSection.vue';
import AgentPerformanceSection from '../Components/OwnerDashboard/AgentPerformanceSection.vue';
import AttendanceSection from '../Components/OwnerDashboard/AttendanceSection.vue';
import RetentionSection from '../Components/OwnerDashboard/RetentionSection.vue';
import AlertsSection from '../Components/OwnerDashboard/AlertsSection.vue';
import { BarChart3, Users, Landmark, UserCheck, Star, ShieldAlert, PhoneCall, TrendingUp } from 'lucide-vue-next';

const props = defineProps({
    initialFilters: { type: Object, required: true },
    centers: { type: Array, default: () => [] },
    salesSummary: { type: Object, required: true },
    teamLeaderboard: { type: Object, required: true },
    agentPerformance: { type: Object, required: true },
    attendance: { type: Object, required: true },
    retention: { type: Object, required: true },
    alerts: { type: Array, default: () => [] },
    closerStats: { type: Array, default: () => [] },
    closedCalls: { type: Object, required: true },
    filters: { type: Object, required: true },
    leadsSummary: { type: Object, required: true },
});

const currentTab = ref('overview');
const loading = ref(false);

const tabs = [
    { id: 'overview', label: 'Sales Summary', icon: BarChart3 },
    { id: 'leads_summary', label: 'Leads Summary', icon: TrendingUp },
    { id: 'closed_calls', label: 'Daily Sales Stats', icon: PhoneCall },
    { id: 'teams', label: 'Team Leaderboard', icon: Landmark },
    { id: 'agents', label: 'Agent & Closer', icon: Users },
    { id: 'attendance', label: 'Attendance', icon: UserCheck },
    { id: 'retention', label: 'Retention & QA', icon: Star },
    { id: 'alerts', label: 'Alerts', icon: ShieldAlert },
];

const handleFilterChange = (newFilters) => {
    loading.value = true;
    router.get('/app/owner-dashboard', newFilters, {
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
    <AppLayout title="Owner Dashboard">
        <Head title="Owner Dashboard — Executive Report" />

        <div class="space-y-6">
            <!-- Filter Bar -->
            <PeriodFilter
                :centers="centers"
                :initial-period="initialFilters.period"
                :initial-start-date="initialFilters.start_date"
                :initial-end-date="initialFilters.end_date"
                :initial-center-id="initialFilters.center_id"
                @change="handleFilterChange"
            />

            <!-- Navigation Tabs -->
            <div class="border-b border-gray-200">
                <nav class="flex flex-wrap gap-2 -mb-px" aria-label="Tabs">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        type="button"
                        class="flex items-center gap-2 border-b-2 px-4 py-3.5 text-sm font-medium transition cursor-pointer"
                        :class="
                            currentTab === tab.id
                                ? 'border-indigo-600 text-indigo-600'
                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                        "
                        @click="currentTab = tab.id"
                    >
                        <component :is="tab.icon" :size="16" />
                        <span>{{ tab.label }}</span>
                        <!-- Alert Badge -->
                        <span
                            v-if="tab.id === 'alerts' && alerts.length > 0"
                            class="ml-1.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white"
                        >
                            {{ alerts.length }}
                        </span>
                    </button>
                </nav>
            </div>

            <!-- Tab Content Pane -->
            <div class="relative min-h-[400px]">
                <!-- Loading Overlay -->
                <div
                    v-if="loading"
                    class="absolute inset-0 z-10 flex items-center justify-center bg-gray-50/60 rounded-xl"
                >
                    <div class="flex flex-col items-center gap-3">
                        <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-indigo-600"></div>
                        <span class="text-sm font-medium text-gray-500">Refreshing dashboard metrics...</span>
                    </div>
                </div>

                <div :class="{ 'opacity-50 pointer-events-none transition-opacity duration-200': loading }">
                    <SalesSummarySection
                        v-if="currentTab === 'overview'"
                        :data="salesSummary"
                        :closer-stats="closerStats"
                        :loading="loading"
                    />



                    <LeadsSummarySection
                        v-if="currentTab === 'leads_summary'"
                        :data="leadsSummary"
                        :loading="loading"
                    />

                    <ClosedCallsSection
                        v-if="currentTab === 'closed_calls'"
                        :closed-calls="closedCalls"
                        :filters="filters"
                        :loading="loading"
                    />

                    <TeamLeaderboardSection
                        v-if="currentTab === 'teams'"
                        :data="teamLeaderboard"
                        :loading="loading"
                    />

                    <AgentPerformanceSection
                        v-if="currentTab === 'agents'"
                        :data="agentPerformance"
                        :loading="loading"
                    />

                    <AttendanceSection
                        v-if="currentTab === 'attendance'"
                        :data="attendance"
                        :loading="loading"
                    />

                    <RetentionSection
                        v-if="currentTab === 'retention'"
                        :data="retention"
                        :loading="loading"
                    />

                    <AlertsSection
                        v-if="currentTab === 'alerts'"
                        :alerts="alerts"
                        :loading="loading"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
