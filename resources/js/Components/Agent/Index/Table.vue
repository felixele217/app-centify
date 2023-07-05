<script setup lang="ts">
import PageHeader from '@/Components/PageHeader.vue'
import type User from '@/types/Admin'
import euroDisplay from '@/utils/euroDisplay'
import { ref } from 'vue'
import CreateAgentSlideOver from './CreateAgentSlideOver.vue'

defineProps<{
    agents: Array<User>
}>()

const isOpen = ref(false)
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

    <div class="-mx-4 overflow-hidden bg-white ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
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
            </thead>
            <tbody>
                <tr
                    v-for="(agent, agentIdx) in agents"
                    :key="agent.id"
                >
                    <td
                        :class="[
                            agentIdx === 0 ? '' : 'border-t border-transparent',
                            'relative py-4 pl-4 pr-3 text-sm sm:pl-6',
                        ]"
                    >
                        <div class="font-medium text-gray-900">
                            {{ agent.name }}
                        </div>

                        <div
                            v-if="agentIdx !== 0"
                            class="absolute -top-px left-6 right-0 h-px bg-gray-200"
                        />
                    </td>
                    <td
                        :class="[
                            agentIdx === 0 ? '' : 'border-t border-gray-200',
                            'hidden px-3 py-3.5 text-sm text-gray-500 lg:table-cell',
                        ]"
                    >
                        {{ agent.email }}
                    </td>
                    <td
                        :class="[
                            agentIdx === 0 ? '' : 'border-t border-gray-200',
                            'hidden px-3 py-3.5 text-sm text-gray-500 lg:table-cell',
                        ]"
                    >
                        {{ euroDisplay(agent.base_salary) }}
                    </td>
                    <td
                        :class="[
                            agentIdx === 0 ? '' : 'border-t border-gray-200',
                            'hidden px-3 py-3.5 text-sm text-gray-500 lg:table-cell',
                        ]"
                    >
                        {{ euroDisplay(agent.on_target_earning) }}
                    </td>
                    <td
                        :class="[
                            agentIdx === 0 ? '' : 'border-t border-transparent',
                            'relative py-3.5 pl-3 pr-4 text-right text-sm font-medium sm:pr-6',
                        ]"
                    >
                        <button
                            type="button"
                            class="inline-flex items-center rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-30 disabled:hover:bg-white"
                        >
                            Edit<span class="sr-only"></span>
                        </button>
                        <div
                            v-if="agentIdx !== 0"
                            class="absolute -top-px left-0 right-6 h-px bg-gray-200"
                        />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
