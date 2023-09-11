<script setup lang="ts">
const props = defineProps<{
    agentName: string
    demoScheduledPercentage: number
    dealWonPercentage: number
    withArr: boolean
}>()
</script>

<template>
    <p>
        <span class="font-semibold">
            {{ props.agentName }}
        </span>
        retains
        <span class="font-semibold"> {{ props.demoScheduledPercentage }}% SAO</span>

        <span v-if="props.withArr">
            ,
            <span class="font-semibold"> {{ props.dealWonPercentage }}% ARR</span>.
        </span>

        <span v-else>.</span>
    </p>
</template>
