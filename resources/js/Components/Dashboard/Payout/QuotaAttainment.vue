<script setup lang="ts">
import Agent from '@/types/Agent'
import { TimeScopeEnum } from '@/types/Enum/TimeScopeEnum'
import getRollingQuota from '@/utils/Date/rollingQuota'
import queryParamValue from '@/utils/queryParamValue'
import roundFloat from '@/utils/roundFloat'
import sum from '@/utils/sum'
import Card from '../../Card.vue'
import DoughnutChart from './DoughnutChart/Content.vue'

const props = defineProps<{
    agents: Array<Agent>
}>()

const rollingQuota = getRollingQuota(queryParamValue('time_scope') as TimeScopeEnum)

const averageAchievedQuotaAttainment =
    sum(props.agents.map((agent) => agent.quota_attainment!)) / props.agents.length / rollingQuota
</script>

<template>
    <Card class="flex justify-between">
        <div class="flex h-full flex-col justify-between">
            <p class="font-semibold">Average Quota Attainment</p>
            <div>
                <h2 class="mb-3">All Teams</h2>
                <p class="font-semibold text-gray-400">rolling quota: {{ roundFloat(rollingQuota * 100) }}%</p>
            </div>
        </div>

        <DoughnutChart :averageAchievedQuotaAttainment="averageAchievedQuotaAttainment * 100" />
    </Card>
</template>
