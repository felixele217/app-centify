<script setup lang="ts">
import EditAndDeleteOptions from '@/Components/Dropdown/EditAndDeleteOptions.vue'
import Modal from '@/Components/Modal.vue'
import PageHeader from '@/Components/PageHeader.vue'
import TableWrapper from '@/Components/TableWrapper.vue'
import Agent from '@/types/Agent'
import { AgentStatusEnum } from '@/types/Enum/AgentStatusEnum'
import euroDisplay from '@/utils/euroDisplay'
import notify from '@/utils/notify'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import UpsertAgentSlideOver from './UpsertAgentSlideOver.vue'

const props = defineProps<{
    agents: Array<Agent>
    possibleStatuses: Array<AgentStatusEnum>
}>()

function deleteAgent(agentId: number): void {
    router.delete(route('agents.destroy', agentId), {
        onSuccess: () => {
            notify('Agent deleted', 'This agent does not exist in your organization any more now.')
            agentIdBeingDeleted.value = null
        },
    })
}

function closeModal(): void {
    isOpen.value = false
    agentBeingEdited.value = undefined
}

const isOpen = ref(false)
const agentIdBeingDeleted = ref<number | null>()
const agentBeingEdited = ref<Agent>()

const src =
    'https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80'
</script>

<template>
    <upsert-agent-slide-over
        @close-slide-over="closeModal"
        :is-open="!!(isOpen || agentBeingEdited)"
        dusk="slide-over-modal"
        :agent="agentBeingEdited"
        :possible-statuses="props.possibleStatuses"
    />

    <page-header
        title="Agents"
        description="Overview of all your agents."
        button-text="Create Agent"
        @button-clicked="isOpen = true"
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
                    class="hidden px-3 py-3.5 text-left text-sm font-semibold text-gray-900 lg:table-cell"
                >
                    E-Mail
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
                <td class="flex items-center gap-5 whitespace-nowrap py-5 pl-5 pr-3 text-sm">
                    <img
                        class="h-10 w-10 rounded-full"
                        :src="src"
                        alt="Profile Photo"
                    />

                    <div class="font-medium text-gray-900">{{ agent.name }}</div>

                    <div
                        v-if="agentId !== 0"
                        class="absolute -top-px left-6 right-0 h-px bg-gray-200"
                    />
                </td>
                <td
                    :class="[
                        agentId === 0 ? '' : 'border-t border-gray-200',
                        'hidden px-3 py-3.5 text-sm text-gray-500 lg:table-cell',
                    ]"
                >
                    {{ agent.email }}
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
                        @edit-action="agentBeingEdited = agent"
                        @delete-action="agentIdBeingDeleted = agent.id"
                        relative-table-style="relative -mt-0.75 top-8 right-8"
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
