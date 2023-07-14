<script setup lang="ts">
import Agent from '@/types/Agent'
import daysInMonth from '@/utils/daysInMonth'
import roundFloat from '@/utils/roundFloat'
import sum from '@/utils/sum'
import Card from '../../Card.vue'
import DoughnutChart from './DoughnutChart/Content.vue'

const props = defineProps<{
    agents: Array<Agent>
}>()

const percentageOfMonthCompleted = new Date().getDate() / daysInMonth()
const averageAchievedQuotaAttainment = sum(props.agents.map((agent) => agent.quota_attainment!)) / props.agents.length

const rollingQuota = roundFloat(percentageOfMonthCompleted * 100, 0)
</script>

<template>
    <Card class="flex justify-between">
        <div>
            <p class="mb-3 font-semibold">Average Quota Attainment</p>
            <h1 class="mb-8">All Teams</h1>
            <p class="font-semibold text-gray-400">rolling quota: {{ rollingQuota }}%</p>
        </div>

        <DoughnutChart :averageAchievedQuotaAttainment="averageAchievedQuotaAttainment * 100" />
    </Card>
</template>
