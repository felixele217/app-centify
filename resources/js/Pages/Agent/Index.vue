<script setup lang="ts">
import Table from '@/Components/Agent/Index/Table.vue'
import type User from '@/types/User'

const props = defineProps<{
    agents: Array<User>
}>()
</script>

<template>
    <Table />
</template>
