<script setup lang="ts">
const props = defineProps<{
    agentName: string
    agentSharePercentage: number
}>()
</script>

<template>
    <p>
        <span class="font-semibold">
            {{ props.agentName }}
        </span>
        retains
        <span class="font-semibold"> {{ props.agentSharePercentage }}% </span>
        of the deal.
    </p>
</template>
