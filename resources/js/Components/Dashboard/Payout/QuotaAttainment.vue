<script setup lang="ts">
import InfoIcon from '@/Components/Icon/InfoIcon.vue'
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

const averageAchievedQuotaAttainment = props.agents.length
    ? sum(props.agents.map((agent) => agent.quota_attainment!)) / props.agents.length / rollingQuota
    : 0
</script>

<template>
    <Card class="flex justify-between">
        <div class="flex h-full flex-col justify-between">
            <p class="font-semibold">Average Quota Attainment</p>
            <div>
                <h2 class="mb-3">All Teams</h2>
                <div class="flex items-center gap-1">
                    <InfoIcon
                        placement="left"
                        hoverText="This is the target that needs to be achieved until today in order to hit 100%.
                        It changes based on your filter settings."
                        class="max-w-5 whitespace-pre-line text-gray-400"
                    />
                    <p class="font-semibold text-gray-400">rolling quota: {{ roundFloat(rollingQuota * 100) }}%</p>
                </div>
            </div>
        </div>

        <DoughnutChart :averageAchievedQuotaAttainment="averageAchievedQuotaAttainment * 100" />
    </Card>
</template>
