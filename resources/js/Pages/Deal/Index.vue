<script setup lang="ts">
import Table from '@/Components/Deal/Index/Table.vue'
import Deal from '@/types/Deal'
import Integration from '@/types/Integration'

const props = defineProps<{
    deals: Array<Deal>
    integrations: Array<Integration>
}>()
</script>

<template>
    <Table
        :deals="props.deals"
        :integrations="props.integrations"
    />
</template>
