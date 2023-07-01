<script setup lang="ts">
import PageHeader from '@/Components/PageHeader.vue'
import PlanCard from '@/Components/Plan/PlanCard.vue'
import type Plan from '@/types/Plan'
import { router } from '@inertiajs/vue3'

const props = defineProps<{
    plans: Array<Plan>
}>()
</script>

<template>
    <PageHeader
        title="Plans"
        description="List of all your plans."
        button-text="Create Plan"
        @button-clicked="router.get(route('plans.create'))"
    />

    <PlanCard :plan="props.plans[0]" />
</template>
