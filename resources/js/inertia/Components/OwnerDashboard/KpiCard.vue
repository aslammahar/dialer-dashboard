<script setup>
import { computed } from 'vue';
import { ArrowUpRight, ArrowDownRight, Minus } from 'lucide-vue-next';

const props = defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    delta: { type: Number, default: 0 },
    prefix: { type: String, default: '' },
    suffix: { type: String, default: '' },
    icon: { type: Object, default: null },
    loading: { type: Boolean, default: false },
});

const deltaType = computed(() => {
    if (props.delta > 0) return 'positive';
    if (props.delta < 0) return 'negative';
    return 'neutral';
});

const formattedDelta = computed(() => {
    const abs = Math.abs(props.delta);
    return abs.toFixed(1) + '%';
});
</script>

<template>
    <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
        <div v-if="loading" class="animate-pulse space-y-4">
            <div class="flex justify-between items-center">
                <div class="h-4 w-24 rounded bg-gray-200"></div>
                <div class="h-8 w-8 rounded-full bg-gray-200"></div>
            </div>
            <div class="h-8 w-32 rounded bg-gray-200"></div>
            <div class="h-4 w-16 rounded bg-gray-200"></div>
        </div>

        <div v-else class="flex flex-col justify-between h-full">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-gray-500">{{ label }}</span>
                <div v-if="icon" class="p-2.5 rounded-lg bg-indigo-50 text-indigo-600">
                    <component :is="icon" :size="20" />
                </div>
            </div>

            <div>
                <h3 class="text-3xl font-bold tracking-tight text-gray-900">
                    {{ prefix }}{{ typeof value === 'number' ? value.toLocaleString() : value }}{{ suffix }}
                </h3>

                <div class="flex items-center gap-1.5 mt-2">
                    <span
                        class="inline-flex items-center gap-0.5 rounded-full px-2 py-0.5 text-xs font-semibold"
                        :class="{
                            'bg-emerald-50 text-emerald-700': deltaType === 'positive',
                            'bg-rose-50 text-rose-700': deltaType === 'negative',
                            'bg-gray-50 text-gray-700': deltaType === 'neutral'
                        }"
                    >
                        <ArrowUpRight v-if="deltaType === 'positive'" :size="12" class="shrink-0" />
                        <ArrowDownRight v-if="deltaType === 'negative'" :size="12" class="shrink-0" />
                        <Minus v-if="deltaType === 'neutral'" :size="12" class="shrink-0" />
                        <span>{{ formattedDelta }}</span>
                    </span>
                    <span class="text-xs text-gray-500">vs last period</span>
                </div>
            </div>
        </div>
    </div>
</template>
