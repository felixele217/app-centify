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
        description="Overview of all your commission plans."
        button-text="Create Plan"
        @button-clicked="router.get(route('plans.create'))"
    />

    <div class="grid grid-cols-2 gap-5">
        <PlanCard
            v-for="plan in props.plans"
            :plan="plan"
        />
    </div>

    <p v-if="props.plans.length === 0">You have no plans yet.</p>
</template>
