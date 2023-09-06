<script setup lang="ts">
import queryParamValue from '@/utils/queryParamValue'
import { TimeScopeEnum } from '@/types/Enum/TimeScopeEnum'
import Agent from '@/types/Agent'
import euroDisplay from '@/utils/euroDisplay'
import sum from '@/utils/sum'
import PaidLeaveCard from '../PaidLeave/PaidLeaveCard.vue'
import { SunIcon } from '@heroicons/vue/24/outline'
import SickIcon from '@/Components/Icon/SickIcon.vue'
import BarChart from '@/Components/Dashboard/Payout/BarChart/BarChart.vue'
import DoughnutChart from '@/Components/Dashboard/Payout/DoughnutChart/Content.vue'
import currentScope from '@/utils/Date/currentScope'

const props = defineProps<{
    agent: Agent
}>()

const timeScopeFromQuery = queryParamValue('time_scope') as TimeScopeEnum | ''
</script>

<template>
    <div>
        <h2>Quota Deep Dive</h2>

        <div class="mt-4">
            <p class="mb-0.5 text-lg text-gray-500">
                Average Quota Attainment in
                <span class="font-semibold text-gray-700">{{ currentScope(timeScopeFromQuery) }}</span>
            </p>
            <p class="mt-1 text-2xl font-semibold text-gray-700">{{ props.agent.quota_attainment_in_percent! }}%</p>
        </div>

        <div class="flex justify-center">
            <DoughnutChart
                class="mt-3"
                :items="props.agent.active_plans!.map(plan => ({
                    value: plan.quota_attainment_in_percent / props.agent.active_plans!.length,
                    label: plan.name
                    }))"
            />
        </div>
    </div>
</template>
