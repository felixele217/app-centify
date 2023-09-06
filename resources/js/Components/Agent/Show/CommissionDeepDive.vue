<script setup lang="ts">
import Agent from '@/types/Agent'
import euroDisplay from '@/utils/euroDisplay'
import sum from '@/utils/sum'
import PaidLeaveCard from '../PaidLeave/PaidLeaveCard.vue'
import { SunIcon } from '@heroicons/vue/24/outline'
import SickIcon from '@/Components/Icon/SickIcon.vue'
import BarChart from '@/Components/Dashboard/Payout/BarChart/BarChart.vue'
import currentScope from '@/utils/Date/currentScope'
import queryParamValue from '@/utils/queryParamValue'
import { TimeScopeEnum } from '@/types/Enum/TimeScopeEnum'

const props = defineProps<{
    agent: Agent
}>()

const timeScopeFromQuery = queryParamValue('time_scope') as TimeScopeEnum | ''
</script>

<template>
    <div>
        <h2>Commission Deep Dive</h2>

        <div class="my-7">
            <p class="mb-0.5 text-lg text-gray-500">
                Total Commission in
                <span class="font-semibold text-gray-700">{{ currentScope(timeScopeFromQuery) }}</span>
            </p>
            <p class="mt-1 text-2xl font-semibold text-gray-700">
                {{ euroDisplay(props.agent.commission!) }}
            </p>
        </div>

        <div>
            <div class="mb-8">
                <p class="mb-0.5 text-gray-500">Plan Commissions</p>
                <p class="text-xl font-semibold text-gray-700">
                    {{
                        euroDisplay(
                            sum(props.agent.active_plans!.map((plan) => plan.kicker_commission + plan.quota_commission))
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

        <div class="mt-5">
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
                        <span class="font-semibold text-purple-800">{{ props.agent.sick_leaves_days_count! }}</span>
                        days sick
                    </p>
                </div>
                <div class="flex items-center gap-1.5 rounded-full bg-yellow-100 px-3 py-1 text-yellow-700">
                    <SunIcon class="h-6 w-6" />
                    <p>
                        <span class="font-semibold text-yellow-800">{{ props.agent.vacation_leaves_days_count! }}</span>
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
</template>
