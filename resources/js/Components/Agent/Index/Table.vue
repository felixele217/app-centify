<script setup lang="ts">
import AgentNameColumn from '@/Components/AgentNameColumn.vue'
import EditAndDeleteOptions from '@/Components/Dropdown/EditAndDeleteOptions.vue'
import Modal from '@/Components/Modal.vue'
import PageHeader from '@/Components/PageHeader.vue'
import TableWrapper from '@/Components/TableWrapper.vue'
import Agent from '@/types/Agent'
import Plan from '@/types/Plan/Plan'
import euroDisplay from '@/utils/euroDisplay'
import notify from '@/utils/notify'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import ActivePlans from '../ActivePlans.vue'
import UpsertAgentSlideOver from './UpsertAgentSlideOver.vue'

const props = defineProps<{
    agents: Array<Agent>
    plans: Array<Pick<Plan, 'id' | 'name'>>
}>()

function deleteAgent(agentId: number): void {
    router.delete(route('agents.destroy', agentId), {
        onSuccess: () => {
            notify('Agent deleted', 'This agent does not exist in your organization any more now.')
            agentIdBeingDeleted.value = null
        },
    })
}

function closeUpsertAgentSlideOver() {
    isUpsertingAgent.value = false
    agentBeingEdited.value = undefined
}

function openSlideOver(agentBeingEditedParam: Agent) {
    agentBeingEdited.value = agentBeingEditedParam

    isUpsertingAgent.value = true
}

const isUpsertingAgent = ref(false)

const agentBeingEdited = ref<Agent>()
const agentIdBeingDeleted = ref<number | null>()
</script>

<template>
    <upsert-agent-slide-over
        @close-upsert-agent-slide-over="closeUpsertAgentSlideOver"
        :is-open="!!isUpsertingAgent"
        dusk="upsert-agent-slide-over"
        :agent="agentBeingEdited"
    />

    <page-header
        title="Agents"
        description="Overview of all your agents."
        button-text="Create Agent"
        @button-clicked="isUpsertingAgent = true"
        button-dusk-identifier="upsert-agent-slide-over-button"
    />

    <TableWrapper :no-items-text="props.agents.length ? undefined : 'You have no agents yet.'">
        <template #header>
            <tr>
                <th
                    scope="col"
                    class="py-3.5 pl-5 pr-3 text-left text-sm font-semibold text-gray-900"
                >
                    Name
                </th>
                <th
                    scope="col"
                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >
                    Active Plans
                </th>
                <th
                    scope="col"
                    class="hidden px-3 py-3.5 text-left text-sm font-semibold text-gray-900 lg:table-cell"
                >
                    Base Salary
                </th>
                <th
                    scope="col"
                    class="hidden px-3 py-3.5 text-left text-sm font-semibold text-gray-900 lg:table-cell"
                >
                    On Target Earning
                </th>
                <th />
            </tr>
        </template>
        <template #body>
            <tr
                class="group"
                v-for="(agent, agentId) in agents"
                :key="agent.id"
                :class="agentId === 0 ? '' : 'border-t'"
            >
                <td class="whitespace-nowrap py-5 pl-5 pr-3 text-sm">
                    <AgentNameColumn :agent="agent" />

                    <div
                        v-if="agentId !== 0"
                        class="absolute -top-px left-6 right-0 h-px bg-gray-200"
                    />
                </td>

                <td class="whitespace-pre-wrap px-3 py-5 text-sm text-gray-500">
                    <ActivePlans
                        :plans="props.plans"
                        :agent="agent"
                    />
                </td>

                <td
                    :class="[
                        agentId === 0 ? '' : 'border-t border-gray-200',
                        'hidden px-3 py-3.5 text-sm text-gray-500 lg:table-cell',
                    ]"
                >
                    {{ euroDisplay(agent.base_salary) }}
                </td>
                <td
                    :class="[
                        agentId === 0 ? '' : 'border-t border-gray-200',
                        'hidden px-3 py-3.5 text-sm text-gray-500 lg:table-cell',
                    ]"
                >
                    {{ euroDisplay(agent.on_target_earning) }}
                </td>
                <td class="absolute lg:table-cell">
                    <EditAndDeleteOptions
                        @edit-action="openSlideOver(agent)"
                        @delete-action="agentIdBeingDeleted = agent.id"
                        relative-table-style="relative -mt-1.5 top-8 right-8"
                    />

                    <div
                        v-if="agentId !== 0"
                        class="absolute -top-px left-0 right-6 h-px bg-gray-200"
                    />
                </td>
            </tr>
        </template>
    </TableWrapper>

    <Modal
        @modal-action="deleteAgent(agentIdBeingDeleted!)"
        :is-negative-action="true"
        :isOpen="!!agentIdBeingDeleted"
        @close-modal="agentIdBeingDeleted = null"
        button-text="Delete"
        title="Delete Agent"
        :description="'Are you sure you want to delete this Agent? \nThis will affect organization\'s metrics and is currently irreversible.'"
    />
</template>
