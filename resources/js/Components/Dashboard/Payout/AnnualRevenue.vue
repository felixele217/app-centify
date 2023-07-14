<script setup lang="ts">
import Agent from '@/types/Agent'
import roundFloat from '@/utils/roundFloat'
import sum from '@/utils/sum'
import Card from '../../Card.vue'
import DoughnutChart from './DoughnutChart/Content.vue'
import euroDisplay from '@/utils/euroDisplay'

const props = defineProps<{
    agents: Array<Agent>
}>()

const averageAchievedQuotaAttainment = (sum(props.agents.map(agent => agent.quota_attainment!)) / props.agents.length)
const totalAchievedCommission = sum(props.agents.map((agent) => agent.commission!))
const totalPossibleCommission = euroDisplay(totalAchievedCommission / averageAchievedQuotaAttainment)
</script>

<template>
    <Card class="flex justify-between">
        <div>
            <p class="mb-3 font-semibold">Quota Attainment</p>
            <h1 class="mb-8">ARR acquired</h1>
            <h2 class="mb-3">{{ euroDisplay(totalAchievedCommission) }}</h2>
            <p class="font-semibold text-gray-400">{{ totalPossibleCommission }}</p>
        </div>

        <DoughnutChart :averageAchievedQuotaAttainment="averageAchievedQuotaAttainment * 100" />
    </Card>
</template>
