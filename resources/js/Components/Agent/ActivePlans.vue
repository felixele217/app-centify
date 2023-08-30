<script setup lang="ts">
import Agent from '@/types/Agent'
import Plan from '@/types/Plan/Plan'
import { PencilSquareIcon } from '@heroicons/vue/24/outline'
import { ref } from 'vue'
import SecondaryButton from '../Buttons/SecondaryButton.vue'
import ManageAgentPlansSlideOver from './Index/ManageAgentPlansSlideOver.vue'

const props = defineProps<{
    agent: Agent
    plans: Array<Pick<Plan, 'id' | 'name'>>
}>()

const isManagingAgentPlans = ref(false)
const agentBeingEdited = ref<Agent>()

function openSlideOver() {
    isManagingAgentPlans.value = true
    agentBeingEdited.value = props.agent
}

function closeManagePlanSlideOver() {
    isManagingAgentPlans.value = false
}
</script>

<template>
    <ManageAgentPlansSlideOver
        @close-manage-plan-slide-over="closeManagePlanSlideOver"
        :is-open="!!isManagingAgentPlans"
        dusk="manage-agent-plans-slide-over"
        :agent="agentBeingEdited"
        :plans="props.plans"
    />

    <div
        v-if="props.agent.active_plans?.length"
        class="flex cursor-pointer items-center gap-2 hover:text-black"
        @click="openSlideOver()"
    >
        <p>{{ props.agent.active_plans!.map((activePlan) => activePlan.name).join('\n') }}</p>

        <div>
            <PencilSquareIcon class="-mt-0.25 h-4 w-4" />
        </div>
    </div>

    <SecondaryButton
        v-else
        class="h-7 text-xs"
        @click="openSlideOver()"
        text="+ Add Plan"
        dusk="manage-agent-plans-slide-over-button"
    />
</template>
