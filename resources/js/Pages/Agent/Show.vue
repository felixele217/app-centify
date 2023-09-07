<script setup lang="ts">
import AgentDeepDive from '@/Components/Agent/Show/AgentDeepDive.vue'
import Card from '@/Components/Card.vue'
import Agent from '@/types/Agent'

const props = defineProps<{
    agent: Agent
}>()
</script>

<template>
    <Card class="w-240"> <AgentDeepDive :agent="props.agent" /> </Card>
</template>
