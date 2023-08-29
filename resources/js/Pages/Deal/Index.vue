<script setup lang="ts">
import Table from '@/Components/Deal/Index/Table.vue'
import Deal from '@/types/Deal'
import Integration from '@/types/Integration'
import PaginatedObject from '@/types/PaginatedObject'

const props = defineProps<{
    paginatedDealsObject: PaginatedObject<Deal>
    integrations: Array<Integration>
}>()
</script>

<template>
    <Table
        :deals="props.paginatedDealsObject"
        :integrations="props.integrations"
    />
</template>
