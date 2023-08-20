<script setup lang="ts">
import Checkbox from '@/Components/Form/Checkbox.vue'
import SlideOver from '@/Components/SlideOver.vue'
import Agent from '@/types/Agent'
import Plan from '@/types/Plan/Plan'
import { router } from '@inertiajs/vue3'

const emit = defineEmits<{
    'close-slide-over': []
}>()

const props = defineProps<{
    isOpen: boolean
    agent?: Agent
    plans: Array<Pick<Plan, 'id' | 'name'>>
}>()

function agentIsAssignedToPlan(planName: string) {
    if (props.agent?.active_plans?.map((active_plan) => active_plan.name).includes(planName)) {
        return true
    }

    return false
}

function handleUpdate(planId: number) {
    router.post(route('plans.agents.store', planId), {
        agent_id: props.agent!.id,
    })
}
</script>

<template>
    <SlideOver
        :is-open="props.isOpen"
        @close-slide-over="emit('close-slide-over')"
        title="Manage Plans"
        description="You can manage which plans will affect this agent's commission."
    >
        <p v-for="plan in props.plans">
            <Checkbox
                :label="plan.name"
                :checked="agentIsAssignedToPlan(plan.name)"
                @update:checked="handleUpdate(plan.id)"
            />
        </p>
    </SlideOver>
</template>
