<script setup lang="ts">
import CreateAgentSlideOver from '@/Components/Agent/Index/CreateAgentSlideOver.vue'
import Table from '@/Components/Agent/Index/Table.vue'
import type User from '@/types/User'
import { ref } from 'vue'

const props = defineProps<{
    agents: Array<User>
}>()
</script>

<template>

    <Table />
</template>
