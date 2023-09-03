<script setup lang="ts">
import Table from '@/Components/Agent/Index/Table.vue'
import Agent from '@/types/Agent'
import { AgentStatusEnum } from '@/types/Enum/AgentStatusEnum'
import Plan from '@/types/Plan/Plan'

const props = defineProps<{
    agents: Array<Agent>
    plans: Array<Pick<Plan, 'id' | 'name'>>
}>()
</script>

<template>
    <Table
        :agents="props.agents"
        :plans="props.plans"
    />
</template>
