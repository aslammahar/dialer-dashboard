<script setup>
import { computed } from 'vue';
import DonutChart from './DonutChart.vue';
import { Users, Clock, AlertTriangle, CheckCircle2 } from 'lucide-vue-next';

const props = defineProps({
    data: { type: Object, required: true },
    loading: { type: Boolean, default: false },
});

const chartLabels = ['Present', 'Absent'];
const chartSeries = computed(() => {
    if (!props.data) return [];
    return [props.data.present_count || 0, props.data.absent_count || 0];
});
</script>

<template>
    <div class="space-y-6">
        <!-- Attendance Stats and Donut Chart -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Stats Grid -->
            <div class="space-y-4 lg:col-span-2">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Expected -->
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Expected Today</span>
                        <div class="flex items-baseline justify-between">
                            <span class="text-2xl font-extrabold text-gray-900">{{ data.total_expected || 0 }}</span>
                            <span class="text-xs text-gray-500">Active roster</span>
                        </div>
                    </div>
                    <!-- Present -->
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Present Onsite</span>
                        <div class="flex items-baseline justify-between">
                            <span class="text-2xl font-extrabold text-emerald-600">{{ data.present_count || 0 }}</span>
                            <span class="text-xs text-emerald-600 font-medium">Checked In</span>
                        </div>
                    </div>
                    <!-- Absent -->
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Absent / Offsite</span>
                        <div class="flex items-baseline justify-between">
                            <span class="text-2xl font-extrabold text-rose-600">{{ data.absent_count || 0 }}</span>
                            <span class="text-xs text-gray-500">Not check in</span>
                        </div>
                    </div>
                    <!-- Late -->
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Late Arrivals</span>
                        <div class="flex items-baseline justify-between">
                            <span class="text-2xl font-extrabold text-amber-600">{{ data.late_count || 0 }}</span>
                            <span class="text-xs text-amber-600 font-medium">>5 min delay</span>
                        </div>
                    </div>
                </div>

                <!-- Overall Attendance rate -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-2">Overall Attendance Rate</span>
                    <div class="flex items-center gap-4">
                        <div class="flex-1 bg-gray-100 rounded-full h-4 overflow-hidden">
                            <div
                                class="bg-indigo-600 h-full rounded-full transition-all duration-500"
                                :style="{ width: `${data.attendance_rate || 0}%` }"
                            ></div>
                        </div>
                        <span class="text-lg font-bold text-gray-900 shrink-0">{{ data.attendance_rate || 0 }}%</span>
                    </div>
                </div>
            </div>

            <!-- Donut Chart -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm flex flex-col justify-between">
                <h4 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider text-center">Attendance Breakdown</h4>
                <div v-if="loading" class="h-60 flex items-center justify-center animate-pulse">
                    <div class="h-32 w-32 rounded-full border-8 border-gray-200 border-t-indigo-600 animate-spin"></div>
                </div>
                <div v-else class="flex justify-center">
                    <DonutChart :labels="chartLabels" :series="chartSeries" :height="220" />
                </div>
            </div>
        </div>

        <!-- Detailed lists -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Present List (2/3 width) -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">
                <h4 class="text-md font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <CheckCircle2 :size="18" class="text-emerald-500" />
                    <span>Present Employees</span>
                </h4>

                <div v-if="loading" class="space-y-3 animate-pulse">
                    <div v-for="i in 4" :key="i" class="h-8 bg-gray-100 rounded"></div>
                </div>

                <div v-else-if="!data.present_list || !data.present_list.length" class="h-[200px] w-full flex items-center justify-center bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-400">No onsite check-ins logged today.</span>
                </div>

                <div v-else class="overflow-y-auto max-h-[350px]">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-xs font-semibold text-gray-400 uppercase">
                                <th class="pb-2">Name</th>
                                <th class="pb-2">No</th>
                                <th class="pb-2">Check In</th>
                                <th class="pb-2">Check Out</th>
                                <th class="pb-2 text-right">Punctuality</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <tr v-for="emp in data.present_list" :key="emp.user_id" class="hover:bg-gray-50/50">
                                <td class="py-2.5 font-medium text-gray-900">
                                    {{ emp.name }}
                                </td>
                                <td class="py-2.5 text-gray-500">
                                    {{ emp.employee_no }}
                                </td>
                                <td class="py-2.5 text-gray-700 font-medium">
                                    {{ emp.check_in }}
                                </td>
                                <td class="py-2.5 text-gray-700 font-medium">
                                    {{ emp.check_out }}
                                </td>
                                <td class="py-2.5 text-right font-semibold">
                                    <span
                                        class="inline-block px-2 py-0.5 rounded-full text-xs"
                                        :class="emp.is_late ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700'"
                                    >
                                        {{ emp.late_formatted }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Absent List (1/3 width) -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h4 class="text-md font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <AlertTriangle :size="18" class="text-rose-500" />
                    <span>Absent / Offsite</span>
                </h4>

                <div v-if="loading" class="space-y-3 animate-pulse">
                    <div v-for="i in 4" :key="i" class="h-8 bg-gray-100 rounded"></div>
                </div>

                <div v-else-if="!data.absent_list || !data.absent_list.length" class="h-[200px] w-full flex items-center justify-center bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-400">All scheduled employees present.</span>
                </div>

                <div v-else class="overflow-y-auto max-h-[350px]">
                    <div class="divide-y divide-gray-100">
                        <div v-for="emp in data.absent_list" :key="emp.id" class="py-2.5 flex justify-between items-center hover:bg-gray-50/50 px-1">
                            <div>
                                <span class="font-medium text-gray-900 block">{{ emp.name }}</span>
                                <span class="text-xs text-gray-400">{{ emp.type }}</span>
                            </div>
                            <span class="inline-block h-2 w-2 rounded-full bg-rose-400"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
