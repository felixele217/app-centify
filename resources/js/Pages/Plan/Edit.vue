<script setup lang="ts">
import UpsertPlanCard from '@/Components/Plan/UpsertPlanCard.vue'
import Agent from '@/types/Agent'
import Plan from '@/types/Plan'

const props = defineProps<{
    agents: Array<Pick<Agent, 'id' | 'name'>>
    target_variable_options: Array<string>
    payout_frequency_options: Array<string>
    plan: Plan
}>()
</script>

<template>
    <UpsertPlanCard
        :plan="props.plan"
        :agents="props.agents"
        :target_variable_options="props.target_variable_options"
        :payout_frequency_options="props.payout_frequency_options"
    />
</template>
