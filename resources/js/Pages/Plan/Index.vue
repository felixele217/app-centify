<script setup lang="ts">
import PageHeader from '@/Components/PageHeader.vue'
import PlanCard from '@/Components/Plan/PlanCard.vue'
import type Plan from '@/types/Plan'

const props = defineProps<{
    plans: Array<Plan>
}>()
</script>

<template>
    <PageHeader
        title="Plans"
        description="List of all your plans."
        button-text="Create Plan"
    />

    <PlanCard :plan="props.plans[0]" />
</template>
