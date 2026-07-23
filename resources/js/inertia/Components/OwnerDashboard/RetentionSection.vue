<script setup>
import KpiCard from './KpiCard.vue';
import { ShieldAlert, LifeBuoy, Star, AlertTriangle } from 'lucide-vue-next';

defineProps({
    data: { type: Object, required: true },
    loading: { type: Boolean, default: false },
});

const getScoreColorClass = (score) => {
    if (score >= 85) return 'text-emerald-500';
    if (score >= 75) return 'text-indigo-500';
    if (score >= 60) return 'text-amber-500';
    return 'text-rose-500';
};

const getScoreProgressClass = (score) => {
    if (score >= 85) return 'stroke-emerald-500';
    if (score >= 75) return 'stroke-indigo-600';
    if (score >= 60) return 'stroke-amber-500';
    return 'stroke-rose-500';
};
</script>

<template>
    <div class="space-y-6">
        <!-- KPI Cards Grid -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <KpiCard
                label="Churn / Chargeback Rate"
                :value="data.churn_rate"
                suffix="%"
                :delta="0"
                :icon="ShieldAlert"
                :loading="loading"
            />
            <KpiCard
                label="Policies Saved"
                :value="data.saves_count"
                :delta="0"
                :icon="LifeBuoy"
                :loading="loading"
            />
            <KpiCard
                label="Approved Base Count"
                :value="data.approved_base_count"
                :delta="0"
                :icon="Star"
                :loading="loading"
            />
        </div>

        <!-- QA Quality score gauges -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-6">
                <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600">
                    <Star :size="20" />
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-900">QA Monitoring & Quality Control</h4>
                    <p class="text-xs text-gray-500">Average audit scores for agents and closers from recent QA evaluations</p>
                </div>
            </div>

            <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 gap-8 py-6 animate-pulse">
                <div v-for="i in 2" :key="i" class="flex flex-col items-center space-y-4">
                    <div class="h-28 w-28 rounded-full bg-gray-200"></div>
                    <div class="h-4 w-32 rounded bg-gray-200"></div>
                </div>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8 py-6">
                <!-- Agent QA Gauge -->
                <div class="flex flex-col items-center justify-center border-b border-gray-100 pb-6 md:border-b-0 md:border-r md:border-gray-100 md:pb-0">
                    <div class="relative flex items-center justify-center mb-4">
                        <!-- SVG Circular Gauge -->
                        <svg class="w-32 h-32 transform -rotate-90">
                            <!-- Background Circle -->
                            <circle
                                cx="64"
                                cy="64"
                                r="52"
                                class="stroke-gray-100"
                                stroke-width="8"
                                fill="transparent"
                            />
                            <!-- Foreground Progress Circle -->
                            <circle
                                cx="64"
                                cy="64"
                                r="52"
                                :class="getScoreProgressClass(data.agent_qa_avg || 0)"
                                stroke-width="8"
                                fill="transparent"
                                :stroke-dasharray="326.7"
                                :stroke-dashoffset="326.7 - (326.7 * (data.agent_qa_avg || 0)) / 100"
                                stroke-linecap="round"
                                class="transition-all duration-700 ease-out"
                            />
                        </svg>
                        <div class="absolute text-center">
                            <span class="text-3xl font-extrabold tracking-tight text-gray-900">{{ data.agent_qa_avg || 0 }}%</span>
                        </div>
                    </div>
                    <h5 class="text-sm font-bold text-gray-900 mb-1">Average Agent Audit Score</h5>
                    <p class="text-xs text-gray-400 text-center max-w-[200px]">Evaluations imported from avatar_monitoring records</p>
                </div>

                <!-- Closer QA Gauge -->
                <div class="flex flex-col items-center justify-center">
                    <div class="relative flex items-center justify-center mb-4">
                        <!-- SVG Circular Gauge -->
                        <svg class="w-32 h-32 transform -rotate-90">
                            <!-- Background Circle -->
                            <circle
                                cx="64"
                                cy="64"
                                r="52"
                                class="stroke-gray-100"
                                stroke-width="8"
                                fill="transparent"
                            />
                            <!-- Foreground Progress Circle -->
                            <circle
                                cx="64"
                                cy="64"
                                r="52"
                                :class="getScoreProgressClass(data.closer_qa_avg || 0)"
                                stroke-width="8"
                                fill="transparent"
                                :stroke-dasharray="326.7"
                                :stroke-dashoffset="326.7 - (326.7 * (data.closer_qa_avg || 0)) / 100"
                                stroke-linecap="round"
                                class="transition-all duration-700 ease-out"
                            />
                        </svg>
                        <div class="absolute text-center">
                            <span class="text-3xl font-extrabold tracking-tight text-gray-900">{{ data.closer_qa_avg || 0 }}%</span>
                        </div>
                    </div>
                    <h5 class="text-sm font-bold text-gray-900 mb-1">Average Closer Audit Score</h5>
                    <p class="text-xs text-gray-400 text-center max-w-[200px]">Evaluations imported from closer monitoring logs</p>
                </div>
            </div>
        </div>
    </div>
</template>
