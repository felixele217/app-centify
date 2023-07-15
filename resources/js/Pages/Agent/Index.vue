<script setup lang="ts">
import Table from '@/Components/Agent/Index/Table.vue'
import Agent from '@/types/Agent'
import { AgentStatusEnum } from '@/types/Enum/AgentStatusEnum'

const props = defineProps<{
    agents: Array<Agent>
    possible_statuses: Array<AgentStatusEnum>
}>()
</script>

<template>
    <Table
        :agents="props.agents"
        :possible-statuses="props.possible_statuses"
    />
</template>
