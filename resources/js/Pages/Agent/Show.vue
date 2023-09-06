<script setup lang="ts">
import Filter from '@/Components/Form/Filter.vue'
import Agent from '@/types/Agent'
import euroDisplay from '@/utils/euroDisplay'
import DoughnutChart from '@/Components/Dashboard/Payout/DoughnutChart/Content.vue'
import Card from '@/Components/Card.vue'
import roundFloat from '@/utils/roundFloat'
import BarChart from '@/Components/Dashboard/Payout/BarChart/BarChart.vue'
import TotalCommission from '@/Components/Dashboard/Payout/TotalCommission.vue'
import { BanknotesIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
    agent: Agent
}>()
</script>

<template>
    <div class="w-216">
        <div class="flex justify-between">
            <Card>
                <h1>{{ agent.name }}</h1>

                <div class="mt-5">
                    <p class="mb-0.5 text-gray-500">Base Salary</p>
                    <p class="text-xl font-semibold text-gray-700">{{ euroDisplay(props.agent.base_salary) }}</p>
                </div>

                <div class="mt-4">
                    <p class="mb-0.5 text-gray-500">On Target Earning</p>
                    <p class="text-xl font-semibold text-gray-700">{{ euroDisplay(props.agent.on_target_earning) }}</p>
                </div>
            </Card>

            <Card class="flex h-full flex-col justify-between">
                <div>
                    <h3 class="mb-0.5">Total Commission</h3>
                    <p class="text-xl font-semibold text-gray-700">{{ euroDisplay(props.agent.commission!) }}</p>
                </div>

                <div class="mt-5">
                    <h3 class="mb-0.5">Total Quota Attainment</h3>
                    <p class="text-xl font-semibold text-gray-700">{{ props.agent.quota_attainment_in_percent }}%</p>
                </div>
            </Card>

            <div>
                <Filter :reload-url="route('agents.show', props.agent.id)" />
            </div>
        </div>

        <Card class="mt-5 flex justify-between gap-10">
            <div>
                <div>
                    <div class="mb-5">
                        <p class="mb-0.5 text-gray-500">Plan Commissions</p>
                        <p class="text-xl font-semibold text-gray-700">{{ euroDisplay(props.agent.commission!) }}</p>
                    </div>

                    <BarChart
                        class=""
                        :items="props.agent.active_plans!.map(plan => ({
                            label: plan.name,
                            quotaCommission: plan.quota_commission,
                            kickerCommission: plan.kicker_commission
                        }))"
                    />
                </div>

                <div
                    class="mt-6"
                    v-for="plan in props.agent.active_plans"
                >
                    <p class="text-lg font-semibold">{{ plan.name }}</p>

                    <div class="mt-2 grid w-80 grid-cols-2 space-y-0.5 text-gray-600">
                        <p class>Quota Attainment:</p>
                        <p class="text-right">{{ plan.quota_attainment_in_percent! }}%</p>
                        <p>Quota Commission:</p>
                        <p class="text-right">{{ euroDisplay(plan.quota_commission) }}</p>
                        <p>Kicker Commission:</p>
                        <p class="text-right">{{ euroDisplay(plan.kicker_commission) }}</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-end">
                <p class="mb-0.5 text-gray-500">Total Quota Attainment</p>
                <DoughnutChart
                    class="mt-5"
                    :quotaAttainment="agent.quota_attainment_in_percent!"
                />
            </div>
        </Card>
    </div>
</template>
