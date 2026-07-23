<script setup>
import { Users, Phone, ShieldCheck } from 'lucide-vue-next';

defineProps({
    data: { type: Object, required: true },
    loading: { type: Boolean, default: false },
});
</script>

<template>
    <div class="space-y-6">
        <!-- Top Agents and Closers side-by-side -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Top Agents (Leads) -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 rounded-lg bg-orange-50 text-orange-600">
                        <Users :size="20" />
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900">Top Agents (Lead Volume)</h4>
                        <p class="text-xs text-gray-500">Ranked by count of submitted avatar leads</p>
                    </div>
                </div>

                <div v-if="loading" class="space-y-4 animate-pulse">
                    <div v-for="i in 5" :key="i" class="h-10 bg-gray-100 rounded"></div>
                </div>

                <div v-else-if="!data.top_agents || !data.top_agents.length" class="h-[300px] w-full flex items-center justify-center bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-400">No agent data found.</span>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-xs font-semibold text-gray-400 uppercase">
                                <th class="pb-2">Agent Name</th>
                                <th class="pb-2 text-right">Leads</th>
                                <th class="pb-2 text-right">Sales Made</th>
                                <th class="pb-2 text-right">Conv. %</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <tr v-for="agent in data.top_agents" :key="agent.dialer_id" class="hover:bg-gray-50/50">
                                <td class="py-2.5 font-medium text-gray-900">
                                    {{ agent.agent_name }}
                                </td>
                                <td class="py-2.5 text-right text-gray-600 font-medium">
                                    {{ agent.lead_count }}
                                </td>
                                <td class="py-2.5 text-right text-gray-600 font-medium">
                                    {{ agent.sales_made }}
                                </td>
                                <td class="py-2.5 text-right text-orange-600 font-bold">
                                    {{ agent.conversion_rate }}%
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Closers (Deals) -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600">
                        <ShieldCheck :size="20" />
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900">Top Closers (Approved Deals)</h4>
                        <p class="text-xs text-gray-500">Ranked by count of approved closed calls</p>
                    </div>
                </div>

                <div v-if="loading" class="space-y-4 animate-pulse">
                    <div v-for="i in 5" :key="i" class="h-10 bg-gray-100 rounded"></div>
                </div>

                <div v-else-if="!data.top_closers || !data.top_closers.length" class="h-[300px] w-full flex items-center justify-center bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-400">No closer data found.</span>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-xs font-semibold text-gray-400 uppercase">
                                <th class="pb-2">Closer Name</th>
                                <th class="pb-2 text-right">Submissions</th>
                                <th class="pb-2 text-right">Approved</th>
                                <th class="pb-2 text-right">Premium</th>
                                <th class="pb-2 text-right">Approval %</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <tr v-for="closer in data.top_closers" :key="closer.closer_id" class="hover:bg-gray-50/50">
                                <td class="py-2.5 font-medium text-gray-900">
                                    {{ closer.closer_name }}
                                </td>
                                <td class="py-2.5 text-right text-gray-600 font-medium">
                                    {{ closer.total_submissions }}
                                </td>
                                <td class="py-2.5 text-right text-gray-600 font-semibold">
                                    {{ closer.approved_deals }}
                                </td>
                                <td class="py-2.5 text-right text-indigo-600 font-bold">
                                    ${{ parseFloat(closer.approved_premium || 0).toLocaleString() }}
                                </td>
                                <td class="py-2.5 text-right text-emerald-600 font-bold">
                                    {{ closer.approval_rate }}%
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Talk Time Leaders -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-4">
                <div class="p-2 rounded-lg bg-emerald-50 text-emerald-600">
                    <Phone :size="20" />
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-900">Talk Time Leaders</h4>
                    <p class="text-xs text-gray-500">Aggregated daily talk times and imported submitted vs approved counts</p>
                </div>
            </div>

            <div v-if="loading" class="space-y-4 animate-pulse">
                <div v-for="i in 5" :key="i" class="h-10 bg-gray-100 rounded"></div>
            </div>

            <div v-else-if="!data.talktime_leaders || !data.talktime_leaders.length" class="h-[250px] w-full flex items-center justify-center bg-gray-50 rounded-lg">
                <span class="text-sm text-gray-400">No talk time data found in this period range. Make sure daily performance data is imported.</span>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 text-xs font-semibold text-gray-400 uppercase">
                            <th class="pb-2">Employee No</th>
                            <th class="pb-2">Employee Name</th>
                            <th class="pb-2 text-right">Talk Time</th>
                            <th class="pb-2 text-right">Submitted Sales</th>
                            <th class="pb-2 text-right">Approved Sales</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <tr v-for="leader in data.talktime_leaders" :key="leader.employee_id" class="hover:bg-gray-50/50">
                            <td class="py-2.5 text-gray-500 font-medium">
                                {{ leader.employee_id }}
                            </td>
                            <td class="py-2.5 font-medium text-gray-900">
                                {{ leader.employee_name }}
                            </td>
                            <td class="py-2.5 text-right text-emerald-600 font-bold">
                                {{ leader.talktime_formatted }}
                            </td>
                            <td class="py-2.5 text-right text-gray-600 font-medium">
                                {{ leader.submitted_sales }}
                            </td>
                            <td class="py-2.5 text-right text-indigo-600 font-bold">
                                {{ leader.approved_sales }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
