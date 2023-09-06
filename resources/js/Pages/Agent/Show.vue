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
import currentScope from '@/utils/Date/currentScope'
import queryParamValue from '@/utils/queryParamValue'
import { TimeScopeEnum } from '@/types/Enum/TimeScopeEnum'
import { computed } from 'vue'
import PaidLeaveCard from '@/Components/Agent/PaidLeave/PaidLeaveCard.vue'
import sum from '@/utils/sum'

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

                <div class="flex gap-20">
                    <div>
                        <div class="mt-5">
                            <p class="mb-0.5 text-gray-500">Base Salary</p>
                            <p class="text-xl font-semibold text-gray-700">
                                {{ euroDisplay(props.agent.base_salary) }}
                            </p>
                        </div>
                        <div class="mt-4">
                            <p class="mb-0.5 text-gray-500">On Target Earning</p>
                            <p class="text-xl font-semibold text-gray-700">
                                {{ euroDisplay(props.agent.on_target_earning) }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <div class="mt-5">
                            <p class="mb-0.5 text-gray-500">
                                Total Commission in {{ currentScope(timeScopeFromQuery) }}
                            </p>
                            <p class="text-xl font-semibold text-gray-700">
                                {{ euroDisplay(props.agent.commission!) }}
                            </p>
                        </div>
                        <div class="mt-4">
                            <p class="mb-0.5 text-gray-500">
                                Total Quota Attainment in {{ currentScope(timeScopeFromQuery) }}
                            </p>
                            <p class="text-xl font-semibold text-gray-700">
                                {{ props.agent.quota_attainment_in_percent }}%
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <Filter :reload-url="route('agents.show', props.agent.id)" />
            </div>
        </Card>

        <Card class="mt-5">
            <div>
                <h1 class="mb-5">Commission Deep Dive</h1>

                <div class="grid grid-cols-2 gap-10">
                    <div>
                        <div class="mb-8">
                            <p class="mb-0.5 text-gray-500">Plan Commissions</p>
                            <p class="text-xl font-semibold text-gray-700">
                                {{
                                    euroDisplay(
                                        sum(
                                            props.agent.active_plans!.map(
                                                (plan) => plan.kicker_commission + plan.quota_commission
                                            )
                                        )
                                    )
                                }}
                            </p>
                        </div>
                        <BarChart
                            v-if="props.agent.commission"
                            :items="props.agent.active_plans!.map(plan => ({
                                label: plan.name,
                                quotaCommission: plan.quota_commission,
                                kickerCommission: plan.kicker_commission
                            }))"
                        />
                    </div>

                    <div>
                        <div class="mb-6">
                            <p class="mb-0.5 text-gray-500">Paid Leave Commissions</p>
                            <p class="text-xl font-semibold text-gray-700">
                                {{ euroDisplay(props.agent.paid_leaves_commission!) }}
                            </p>
                        </div>

                        <PaidLeaveCard
                            v-for="paidLeave of agent?.paid_leaves"
                            :key="paidLeave.id"
                            :paid-leave="paidLeave"
                            @deleted-paid-leave="$emit('deleted-paid-leave')"
                        />
                    </div>
                </div>
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
        </Card>
        <div class="flex flex-col items-end">
            <p class="mb-0.5 text-gray-500">Total Quota Attainment</p>
            <DoughnutChart
                class="mt-5"
                :quotaAttainment="agent.quota_attainment_in_percent!"
            />
        </div>
    </div>
</template>
