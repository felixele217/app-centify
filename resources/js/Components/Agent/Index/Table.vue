<script setup lang="ts">
import TertiaryButton from '@/Components/Buttons/TertiaryButton.vue'
import Modal from '@/Components/Modal.vue'
import PageHeader from '@/Components/PageHeader.vue'
import Table from '@/Components/Table.vue'
import Agent from '@/types/Agent'
import euroDisplay from '@/utils/euroDisplay'
import notify from '@/utils/notify'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import CreateAgentSlideOver from './CreateAgentSlideOver.vue'

const props = defineProps<{
    agents: Array<Agent>
}>()

function deleteAgent(agentId: number): void {
    router.delete(route('agents.destroy', agentId), {
        onSuccess: () => {
            notify('Agent deleted', 'This agent does not exist in your organization any more now.')
            agentIdBeingDeleted.value = null
        },
    })
}

const isOpen = ref(false)
const agentIdBeingDeleted = ref<number | null>()
</script>

<template>
    <create-agent-slide-over
        @close-slide-over="isOpen = false"
        :is-open="isOpen"
        dusk="slide-over-modal"
    />

    <page-header
        title="Agents"
        description="List of all your agents."
        button-text="Create Agent"
        @button-clicked="isOpen = true"
    />

    <Table :no-items-text="props.agents.length ? undefined : 'You have no agents yet.'">
        <template #header>
            <tr>
                <th
                    scope="col"
                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"
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
                v-for="(agent, agentId) in agents"
                :key="agent.id"
                :class="agentId === 0 ? '' : 'border-t'"
            >
                <td class="relative py-4 pl-4 text-sm sm:pl-6">
                    <div class="font-medium text-gray-900">
                        {{ agent.name }}
                    </div>

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
                <td class="'relative sm:pr-6', flex gap-5 py-3.5 pl-3 pr-4 text-right text-sm font-medium">
                    <TertiaryButton text="Edit" />

                    <TertiaryButton
                        text="Delete"
                        @click="agentIdBeingDeleted = agent.id"
                    />
                   
                    <div
                        v-if="agentId !== 0"
                        class="absolute -top-px left-0 right-6 h-px bg-gray-200"
                    />
                </td>
            </tr>
        </template>
    </Table>

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
