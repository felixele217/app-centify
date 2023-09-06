<script setup lang="ts">
import Filter from '@/Components/Form/Filter.vue'
import Agent from '@/types/Agent'
import euroDisplay from '@/utils/euroDisplay'
import DoughnutChart from '@/Components/Dashboard/Payout/DoughnutChart/Content.vue'
import Card from '@/Components/Card.vue'
import roundFloat from '@/utils/roundFloat'
import BarChart from '@/Components/Dashboard/Payout/BarChart/BarChart.vue'
import TotalCommission from '@/Components/Dashboard/Payout/TotalCommission.vue'
import { BanknotesIcon, SunIcon } from '@heroicons/vue/24/outline'
import currentScope from '@/utils/Date/currentScope'
import queryParamValue from '@/utils/queryParamValue'
import { TimeScopeEnum } from '@/types/Enum/TimeScopeEnum'
import { computed } from 'vue'
import PaidLeaveCard from '@/Components/Agent/PaidLeave/PaidLeaveCard.vue'
import sum from '@/utils/sum'
import SickIcon from '@/Components/Icon/SickIcon.vue'

const props = defineProps<{
    agent: Agent
}>()

const timeScopeFromQuery = queryParamValue('time_scope') as TimeScopeEnum | ''
</script>

<template>
    <div class="w-216">
        <Card class="flex justify-between">
            <div>
                <div class="flex gap-10">
                    <div class="mr-10">
                        <h1>{{ props.agent.name }}</h1>
                        <p class="mt-1 text-gray-500">{{ props.agent.email }}</p>
                    </div>

                    <div>
                        <p class="mb-0.5 text-sm text-gray-500">Base Salary</p>
                        <p class="text-lg font-semibold text-gray-700">
                            {{ euroDisplay(props.agent.base_salary) }}
                        </p>
                    </div>

                    <div>
                        <p class="mb-0.5 text-sm text-gray-500">On Target Earning</p>
                        <p class="text-lg font-semibold text-gray-700">
                            {{ euroDisplay(props.agent.on_target_earning) }}
                        </p>
                    </div>
                </div>

                <div class="mt-10 flex gap-20">
                    <div>
                        <p class="mb-0.5 text-lg text-gray-500">
                            Total Quota Attainment in
                            <span class="font-semibold text-gray-700">{{ currentScope(timeScopeFromQuery) }}</span>
                        </p>
                        <DoughnutChart
                            class="mt-5"
                            :values="props.agent.active_plans!.map(plan => plan.quota_attainment_in_percent / props.agent.active_plans!.length)"
                        />
                    </div>
                    <div>
                        <p class="mb-0.5 text-lg text-gray-500">
                            Total Commission in
                            <span class="font-semibold text-gray-700">{{ currentScope(timeScopeFromQuery) }}</span>
                        </p>
                        <p class="mt-3 text-2xl font-semibold text-gray-700">
                            {{ euroDisplay(props.agent.commission!) }}
                        </p>
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
                        <div class="mb-7 text-gray-500">
                            <p class="mb-0.5">Paid Leave Commissions</p>
                            <p class="text-xl font-semibold text-gray-700">
                                {{ euroDisplay(props.agent.paid_leaves_commission!) }}
                            </p>
                        </div>

                        <div class="mb-3 flex gap-5 text-sm">
                            <div class="flex items-center gap-1.5 rounded-full bg-purple-100 px-3 py-1 text-purple-700">
                                <SickIcon
                                    color="#7e22ce"
                                    size="w-5 h-5"
                                />
                                <p>
                                    <span class="font-semibold text-purple-800">{{
                                        props.agent.sick_leaves_days_count!
                                    }}</span>
                                    days sick
                                </p>
                            </div>
                            <div class="flex items-center gap-1.5 rounded-full bg-yellow-100 px-3 py-1 text-yellow-700">
                                <SunIcon class="h-6 w-6" />
                                <p>
                                    <span class="font-semibold text-yellow-800">{{
                                        props.agent.vacation_leaves_days_count!
                                    }}</span>
                                    days on vacation
                                </p>
                            </div>
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
        </Card>
    </div>
</template>
