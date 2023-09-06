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

        <div class="mt-4">
            <p class="mb-0.5 text-lg text-gray-500">
                Total Commission in
                <span class="font-semibold text-gray-700">{{ currentScope(timeScopeFromQuery) }}</span>
            </p>
            <p class="mt-1 text-2xl font-semibold text-gray-700">
                {{ euroDisplay(props.agent.commission!) }}
            </p>
        </div>

        <div class="mb-5 mt-12">
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

        <div class="mt-12">
            <div class="mb-5 text-gray-500">
                <p class="mb-0.5">Paid Leave Commissions</p>
                <p class="text-xl font-semibold text-gray-700">
                    {{ euroDisplay(props.agent.paid_leaves_commission!) }}
                </p>
            </div>
            <div class="flex gap-5">
                <div>
                    <div class="mb-1 flex items-center gap-1.5">
                        <SickIcon size="w-5 h-5" />
                        <p>
                            <span class="font-semibold">{{ props.agent.sick_leaves_days_count! }}</span>
                            weekdays sick
                        </p>
                    </div>

                    <PaidLeaveCard
                        v-for="paidLeave of props.agent.paid_leaves.filter(
                            (paid_leave) => paid_leave.reason === 'sick'
                        )"
                        :key="paidLeave.id"
                        :paid-leave="paidLeave"
                        @deleted-paid-leave="$emit('deleted-paid-leave')"
                    />
                </div>

                <div>
                    <div class="mb-1 flex items-center gap-1.5">
                        <SunIcon class="h-6 w-6" />
                        <p>
                            <span class="font-semibold">{{ props.agent.vacation_leaves_days_count! }}</span>
                            weekdays on vacation
                        </p>
                    </div>

                    <PaidLeaveCard
                        v-for="paidLeave of props.agent.paid_leaves.filter(
                            (paid_leave) => paid_leave.reason === 'on vacation'
                        )"
                        :key="paidLeave.id"
                        :paid-leave="paidLeave"
                        @deleted-paid-leave="$emit('deleted-paid-leave')"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
