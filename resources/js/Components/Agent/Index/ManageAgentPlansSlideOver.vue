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
    'close-slide-over': []
    'keep-slide-over-open': []
}>()

const props = defineProps<{
    isOpen: boolean
    agent?: Agent
    plans: Array<Pick<Plan, 'id' | 'name'>>
}>()

watch(
    () => props.isOpen,
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
        route('plans.agents.store', [planId, props.agent!.id]),
        {},
        {
            onSuccess: () => {
                notify(
                    'Agent assigned to Plan!',
                    "You succesfully assigned the agent to the plan. The plan will now affect the agent's' commission calculation."
                )
            },
        }
    )
}

function handleDelete(planId: number) {
    router.delete(route('plans.agents.destroy', [planId, props.agent!.id]), {
        onSuccess: () => {
            notify(
                'Agent removed from Plan!',
                "You succesfully removed the agent from the plan. The plan no longer affects the agent's' commission calculation."
            )
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
        @close-slide-over="emit('close-slide-over')"
        title="Manage Plans"
        description="You can manage which plans will affect this agent's commission."
    >
        <p v-for="plan in plansWithAssignmentStates">
            <Checkbox
                :label="plan.name"
                :checked="agentIsAssignedToPlan(plan.name)"
                @update:checked="handleUpdate(plan)"
            />
        </p>
    </SlideOver>
</template>
