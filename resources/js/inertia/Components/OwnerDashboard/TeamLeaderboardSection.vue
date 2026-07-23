<script setup>
import { Trophy, Award } from 'lucide-vue-next';

defineProps({
    data: { type: Object, required: true },
    loading: { type: Boolean, default: false },
});

const getRankClass = (index) => {
    if (index === 0) return 'bg-amber-100 text-amber-800 border-amber-200';
    if (index === 1) return 'bg-slate-100 text-slate-800 border-slate-200';
    if (index === 2) return 'bg-amber-50 text-amber-700 border-amber-100';
    return 'bg-gray-50 text-gray-500 border-gray-100';
};
</script>

<template>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Agent Teams Leaderboard -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-lg bg-orange-50 text-orange-600">
                        <Trophy :size="20" />
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900">Agent Teams Leaderboard</h4>
                        <p class="text-xs text-gray-500">Ranked by volume of leads generated</p>
                    </div>
                </div>
            </div>

            <div v-if="loading" class="space-y-4 animate-pulse">
                <div v-for="i in 5" :key="i" class="h-10 bg-gray-100 rounded"></div>
            </div>

            <div v-else-if="!data.agent_teams || !data.agent_teams.length" class="h-[350px] w-full flex items-center justify-center bg-gray-50 rounded-lg">
                <span class="text-sm text-gray-400">No agent team logs found for this period.</span>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 text-xs font-semibold text-gray-400 uppercase">
                            <th class="pb-3 text-center w-12">Rank</th>
                            <th class="pb-3">Team Name</th>
                            <th class="pb-3 text-right">Leads</th>
                            <th class="pb-3 text-right">Sales Made</th>
                            <th class="pb-3 text-right">Conv. %</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <tr v-for="(team, idx) in data.agent_teams" :key="team.id" class="hover:bg-gray-50/50">
                            <td class="py-3 text-center">
                                <span
                                    class="inline-flex h-6 w-6 items-center justify-center rounded-full border text-xs font-bold"
                                    :class="getRankClass(idx)"
                                >
                                    {{ idx + 1 }}
                                </span>
                            </td>
                            <td class="py-3 font-semibold text-gray-950">
                                {{ team.name }}
                            </td>
                            <td class="py-3 text-right text-gray-600 font-medium">
                                {{ team.total_leads }}
                            </td>
                            <td class="py-3 text-right text-gray-600 font-medium">
                                {{ team.sales_made }}
                            </td>
                            <td class="py-3 text-right text-orange-600 font-bold">
                                {{ team.conversion_rate }}%
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Closer Teams Leaderboard -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600">
                        <Award :size="20" />
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900">Closer Teams Leaderboard</h4>
                        <p class="text-xs text-gray-500">Ranked by deals closed & premium volume</p>
                    </div>
                </div>
            </div>

            <div v-if="loading" class="space-y-4 animate-pulse">
                <div v-for="i in 5" :key="i" class="h-10 bg-gray-100 rounded"></div>
            </div>

            <div v-else-if="!data.closer_teams || !data.closer_teams.length" class="h-[350px] w-full flex items-center justify-center bg-gray-50 rounded-lg">
                <span class="text-sm text-gray-400">No closer team logs found for this period.</span>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 text-xs font-semibold text-gray-400 uppercase">
                            <th class="pb-3 text-center w-12">Rank</th>
                            <th class="pb-3">Team Name</th>
                            <th class="pb-3 text-right">Approved</th>
                            <th class="pb-3 text-right">Premium</th>
                            <th class="pb-3 text-right">Approval %</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <tr v-for="(team, idx) in data.closer_teams" :key="team.id" class="hover:bg-gray-50/50">
                            <td class="py-3 text-center">
                                <span
                                    class="inline-flex h-6 w-6 items-center justify-center rounded-full border text-xs font-bold"
                                    :class="getRankClass(idx)"
                                >
                                    {{ idx + 1 }}
                                </span>
                            </td>
                            <td class="py-3 font-semibold text-gray-950">
                                {{ team.name }}
                            </td>
                            <td class="py-3 text-right text-gray-600 font-medium">
                                {{ team.approved_deals }}
                            </td>
                            <td class="py-3 text-right text-indigo-600 font-bold">
                                ${{ parseFloat(team.approved_premium || 0).toLocaleString(undefined, {minimumFractionDigits: 2}) }}
                            </td>
                            <td class="py-3 text-right text-emerald-600 font-bold">
                                {{ team.approval_rate }}%
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
