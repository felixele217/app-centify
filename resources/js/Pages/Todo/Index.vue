<script setup lang="ts">
import Table from '@/Components/Todo/Index/Table.vue'
import Deal from '@/types/Deal'

const props = defineProps<{
    deals: Array<Deal>
}>()
</script>

<template><Table :deals="props.deals" /></template>
