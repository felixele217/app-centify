<script setup lang="ts">
import Checkbox from '@/Components/Form/Checkbox.vue'
import SlideOver from '@/Components/SlideOver.vue'
import Agent from '@/types/Agent'
import Plan from '@/types/Plan/Plan'
import notify from '@/utils/notify'
import { router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

type PlanWithAssignmentState = {
    id: number
    name: string
    isAssigned: boolean
}

const emit = defineEmits<{
    'close-manage-plan-slide-over': []
}>()

const props = defineProps<{
    isOpen: boolean
    agent?: Agent
    plans: Array<Pick<Plan, 'id' | 'name'>>
}>()

watch(
    () => props.agent,
    async () => {
        plansWithAssignmentStates.value = plansWithAssignmentStates.value.map((plan) => ({
            id: plan.id,
            name: plan.name,
            isAssigned: agentIsAssignedToPlan(plan.name),
        }))
    }
)

function agentIsAssignedToPlan(planName: string) {
    return !!props.agent?.active_plans?.filter((active_plan) => active_plan.name === planName).length
}

function handleUpdate(plan: PlanWithAssignmentState) {
    if (plan.isAssigned) {
        handleDelete(plan.id)
    } else {
        handleStore(plan.id)
    }

    plan.isAssigned = !plan.isAssigned
}

function handleStore(planId: number) {
    router.post(
        route('agents.plans.store', [props.agent!.id, planId]),
        {},
        {
            onSuccess: () => {
                notify('Agent assigned to Plan!', "The plan will now affect the agent's' commission calculation.")
            },
        }
    )
}

function handleDelete(planId: number) {
    router.delete(route('agents.plans.destroy', [props.agent!.id, planId]), {
        onSuccess: () => {
            notify('Agent removed from Plan!', "The plan no longer affects the agent's' commission calculation.")
        },
    })
}

const plansWithAssignmentStates = ref<Array<PlanWithAssignmentState>>(
    props.plans.map((plan) => ({
        id: plan.id,
        name: plan.name,
        isAssigned: agentIsAssignedToPlan(plan.name),
    }))
)
</script>

<template>
    <SlideOver
        :is-open="props.isOpen"
        @close-slide-over="emit('close-manage-plan-slide-over')"
        title="Manage Plans"
        description="You can manage which plans will affect this agent's commission."
    >
        <p class="text-sm text-gray-700">{{ props.agent!.name }} is currently assigned to the following plans:</p>

        <div class="space-y-2">
            <Checkbox
                v-for="plan in plansWithAssignmentStates"
                :label="plan.name"
                :checked="agentIsAssignedToPlan(plan.name)"
                @update:checked="handleUpdate(plan)"
            />
        </div>
    </SlideOver>
</template>
