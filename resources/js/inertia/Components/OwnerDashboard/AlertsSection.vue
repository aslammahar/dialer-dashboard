<script setup>
import { AlertTriangle, AlertCircle, Info, CheckCircle } from 'lucide-vue-next';

defineProps({
    alerts: { type: Array, default: () => [] },
    loading: { type: Boolean, default: false },
});
</script>

<template>
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h4 class="text-lg font-bold text-gray-900 font-sans">System Alerts & Flags</h4>
                <p class="text-xs text-gray-500 font-sans">Automated anomaly detection across agents, queue sales, and attendance logs</p>
            </div>
        </div>

        <div v-if="loading" class="space-y-4 animate-pulse">
            <div v-for="i in 3" :key="i" class="h-16 bg-gray-100 rounded-lg"></div>
        </div>

        <!-- System Healthy state -->
        <div v-else-if="!alerts || !alerts.length" class="flex flex-col items-center justify-center py-12 px-4">
            <div class="h-16 w-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                <CheckCircle :size="36" />
            </div>
            <h5 class="text-md font-bold text-gray-900">All Systems Healthy</h5>
            <p class="text-xs text-gray-400 text-center max-w-[280px] mt-1">
                No active anomalies, stale queues, low attendance rates or chargeback spikes detected.
            </p>
        </div>

        <!-- Alerts List -->
        <div v-else class="space-y-4">
            <div
                v-for="(alert, idx) in alerts"
                :key="idx"
                class="flex items-start gap-4 p-4 rounded-xl border"
                :class="{
                    'bg-rose-50/50 border-rose-100 text-rose-800': alert.type === 'danger',
                    'bg-amber-50/50 border-amber-100 text-amber-800': alert.type === 'warning',
                    'bg-sky-50/50 border-sky-100 text-sky-800': alert.type === 'info',
                }"
            >
                <div
                    class="p-2 rounded-lg"
                    :class="{
                        'bg-rose-100 text-rose-700': alert.type === 'danger',
                        'bg-amber-100 text-amber-700': alert.type === 'warning',
                        'bg-sky-100 text-sky-700': alert.type === 'info',
                    }"
                >
                    <AlertTriangle v-if="alert.type === 'danger'" :size="18" />
                    <AlertCircle v-else-if="alert.type === 'warning'" :size="18" />
                    <Info v-else :size="18" />
                </div>
                <div class="flex-1 min-w-0">
                    <span class="font-bold text-sm block text-gray-900">{{ alert.title }}</span>
                    <span class="text-xs mt-0.5 block text-gray-500 font-medium">{{ alert.message }}</span>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mt-2" v-if="alert.details">
                        Detail: {{ alert.details }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
