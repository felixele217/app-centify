<script setup lang="ts">
import Filter from '@/Components/Form/Filter.vue'
import Agent from '@/types/Agent'
import { TimeScopeEnum } from '@/types/Enum/TimeScopeEnum'
import currentScope from '@/utils/Date/currentScope'
import euroDisplay from '@/utils/euroDisplay'
import queryParamValue from '@/utils/queryParamValue'
import DoughnutChart from '@/Components/Dashboard/Payout/DoughnutChart/Content.vue'
import { CurrencyEuroIcon } from '@heroicons/vue/24/outline'
import Card from '@/Components/Card.vue'
import roundFloat from '@/utils/roundFloat'
import BarChart from '@/Components/Dashboard/Payout/BarChart/BarChart.vue'

const props = defineProps<{
    agent: Agent
}>()

const timeScopeFromQuery = queryParamValue('time_scope') as TimeScopeEnum | ''
</script>

<template>
    <div class="w-216">
        <Card class="flex justify-between">
            <div>
                <h1>{{ agent.name }}</h1>

                <div class="mt-5">
                    <p class="mb-0.5 text-gray-500">Base Salary</p>
                    <p class="text-xl font-semibold text-gray-700">{{ euroDisplay(props.agent.base_salary) }}</p>
                </div>

                <div class="mt-4">
                    <p class="mb-0.5 text-gray-500">On Target Earning</p>
                    <p class="text-xl font-semibold text-gray-700">{{ euroDisplay(props.agent.on_target_earning) }}</p>
                </div>
            </div>
            <div>
                <Filter :reload-url="route('agents.show', props.agent.id)" />
            </div>
        </Card>

        <Card class="mt-5 flex justify-between gap-10">
            <div>
                <div>
                    <div class="mb-5">
                        <p class="mb-0.5 text-gray-500">Total Commission</p>
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
                        <p class="text-right">{{ roundFloat(plan.quota_attainment * 100) }}%</p>
                        <p>Quota Commission:</p>
                        <p class="text-right">{{ euroDisplay(plan.quota_commission) }}</p>
                        <p>Kicker Commission:</p>
                        <p class="text-right">{{ euroDisplay(plan.kicker_commission) }}</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-end">
                <h2>
                    Quota Attainment for
                    {{ currentScope(timeScopeFromQuery) }}
                </h2>
                <DoughnutChart
                    class="mt-5"
                    :quotaAttainment="agent.quota_attainment! * 100 ?? 0"
                />
            </div>
        </Card>
    </div>
</template>
