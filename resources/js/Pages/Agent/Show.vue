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

                <div class="mt-5 grid w-80 grid-cols-2 space-y-0.5 text-gray-600">
                    <p class="font-semibold text-gray-900">Base Salary:</p>
                    <p class="text-right tabular-nums">{{ euroDisplay(agent.base_salary) }}</p>
                    <p class="font-semibold text-gray-900">On Target Earning:</p>
                    <p class="text-right tabular-nums">{{ euroDisplay(agent.on_target_earning) }}</p>
                </div>
            </div>
            <div>
                <Filter :reload-url="route('agents.show', props.agent.id)" />
                <p class="font-semibold text-gray-400">{{ currentScope(timeScopeFromQuery) }}</p>
            </div>
        </Card>

        <Card class="mt-5 flex justify-between gap-10">
            <div>
                <h2 class="mb-4">Commission</h2>

                <div
                    class="mt-6"
                    v-for="plan in props.agent.active_plans"
                >
                    <p class="text-lg font-semibold">{{ plan.name }}</p>

                    <div class="mt-2 grid w-80 grid-cols-2 space-y-0.5 text-gray-600">
                        <p class>Quota Attainment:</p>
                        <p class="text-right">{{ roundFloat(plan.quota_attainment * 100) }}%</p>
                        <p>Quota Commission</p>
                        <p class="text-right">{{ plan.quota_commission ?? 0 }}€</p>
                        <p>Kicker Commission:</p>
                        <p class="text-right">{{ plan.kicker_commission ?? 0 }}€</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-end">
                <h3>Quota Attainment for {{ currentScope(timeScopeFromQuery) }}</h3>
                <DoughnutChart
                    class="mt-5"
                    :quotaAttainment="agent.quota_attainment! * 100 ?? 0"
                />
            </div>
        </Card>
    </div>
</template>
