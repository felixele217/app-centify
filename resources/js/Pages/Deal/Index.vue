<script setup lang="ts">
import Table from '@/Components/Deal/Index/Table.vue'
import Deal from '@/types/Deal'

const props = defineProps<{
    deals: Array<Deal>
}>()
</script>

<template><Table :deals="props.deals" /></template>
