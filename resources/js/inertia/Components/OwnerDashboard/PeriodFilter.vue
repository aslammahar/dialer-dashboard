<script setup>
import { ref, watch } from 'vue';
import { Calendar, Building2 } from 'lucide-vue-next';

const props = defineProps({
    centers: { type: Array, default: () => [] },
    initialPeriod: { type: String, default: 'today' },
    initialStartDate: { type: String, default: '' },
    initialEndDate: { type: String, default: '' },
    initialCenterId: { type: [String, Number], default: '' },
});

const emit = defineEmits(['change']);

const period = ref(props.initialPeriod);
const startDate = ref(props.initialStartDate);
const endDate = ref(props.initialEndDate);
const centerId = ref(props.initialCenterId || '');

const periods = [
    { value: 'today', label: 'Today' },
    { value: 'this_week', label: 'This Week' },
    { value: 'this_month', label: 'This Month' },
    { value: 'custom', label: 'Custom Range' },
];

const handlePeriodChange = (val) => {
    period.value = val;
    if (val !== 'custom') {
        emitChange();
    }
};

const emitChange = () => {
    emit('change', {
        period: period.value,
        start_date: period.value === 'custom' ? startDate.value : null,
        end_date: period.value === 'custom' ? endDate.value : null,
        center_id: centerId.value ? parseInt(centerId.value) : null,
    });
};

watch(centerId, () => {
    emitChange();
});
</script>

<template>
    <div class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm lg:flex-row lg:items-center lg:justify-between">
        <!-- Time Period Toggles -->
        <div class="flex flex-wrap items-center gap-1.5">
            <button
                v-for="p in periods"
                :key="p.value"
                type="button"
                class="rounded-lg px-4 py-2 text-sm font-medium transition"
                :class="
                    period === p.value
                        ? 'bg-indigo-600 text-white shadow-sm'
                        : 'bg-gray-50 text-gray-600 hover:bg-gray-100 hover:text-gray-900'
                "
                @click="handlePeriodChange(p.value)"
            >
                {{ p.label }}
            </button>
        </div>

        <!-- Custom Date Picker Fields -->
        <div v-if="period === 'custom'" class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-1.5">
                <Calendar :size="16" class="text-gray-400" />
                <input
                    v-model="startDate"
                    type="date"
                    class="border-0 p-0 text-sm focus:ring-0 text-gray-700"
                    @change="emitChange"
                />
            </div>
            <span class="text-sm text-gray-400">to</span>
            <div class="flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-1.5">
                <Calendar :size="16" class="text-gray-400" />
                <input
                    v-model="endDate"
                    type="date"
                    class="border-0 p-0 text-sm focus:ring-0 text-gray-700"
                    @change="emitChange"
                />
            </div>
        </div>

        <!-- Center Filter Selector -->
        <div class="flex items-center gap-2.5 min-w-[200px]">
            <div class="flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 w-full">
                <Building2 :size="16" class="text-gray-400 shrink-0" />
                <select
                    v-model="centerId"
                    class="border-0 p-0 text-sm focus:ring-0 text-gray-700 w-full bg-transparent"
                >
                    <option value="">All Offices / Centers</option>
                    <option v-for="c in centers" :key="c.id" :value="c.id">
                        {{ c.center_name.toUpperCase() }}
                    </option>
                </select>
            </div>
        </div>
    </div>
</template>
