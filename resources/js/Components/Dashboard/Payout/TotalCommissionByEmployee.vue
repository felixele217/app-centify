<script setup lang="ts">
import AgentNameColumn from '@/Components/AgentNameColumn.vue'
import Card from '@/Components/Card.vue'
import Filter from '@/Components/Form/Filter.vue'
import PageHeader from '@/Components/PageHeader.vue'
import Agent from '@/types/Agent'
import { AgentStatusEnum } from '@/types/Enum/AgentStatusEnum'
import euroDisplay from '@/utils/euroDisplay'
import roundFloat from '@/utils/roundFloat'
import { FolderArrowDownIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import { usePage } from '@inertiajs/vue3'
import ValueChange from './ValueChange.vue'

const props = defineProps<{
    agents: Array<Agent>
}>()

defineEmits<{
    'open-paid-leave-slide-over': [agentId: number, type: AgentStatusEnum]
}>()

function quotaDisplay(quotaAttainment: number) {
    return roundFloat(quotaAttainment * 100) + '%'
}
</script>

<template>
    <Card>
        <PageHeader
            title="Total Commission by Employee"
            description="Overview of your agents' performances."
        >
            <template #custom-button>
                <a
                    v-if="usePage().props.env !== 'production'"
                    class="mr-5 rounded-full hover:bg-primary-100 bg-primary-50 p-2"
                    :href="route('payouts-export')"
                >
                    <FolderArrowDownIcon class="h-5 w-5" />
                </a>
                <Filter />
            </template>
        </PageHeader>

        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                            <tr>
                                <th
                                    scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                >
                                    Name
                                </th>

                                <th
                                    scope="col"
                                    class="px-5 py-3.5 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                >
                                    Active Plans
                                </th>

                                <th
                                    scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                >
                                    Commission
                                </th>

                                <th
                                    scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                >
                                    Quota Attainment
                                </th>

                                <th
                                    scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                >
                                    Absence
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <div
                                class="my-3 mt-7"
                                v-if="!props.agents.length"
                            >
                                You do not have any agents yet.
                            </div>
                            <tr
                                v-for="agent in props.agents"
                                :key="agent.email"
                            >
                                <td class="whitespace-nowrap py-5 pl-4 pr-3 text-sm sm:pl-0">
                                    <AgentNameColumn :agent="agent" />
                                </td>

                                <td class="whitespace-pre-wrap py-5 pl-4 pr-3 text-sm text-gray-500 sm:pl-0">
                                    {{
                                        agent.active_plans_names!.length
                                            ? agent.active_plans_names!.join('\n')
                                            : 'no active plans'
                                    }}
                                </td>

                                <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <p class="text-900 text-sm">{{ euroDisplay(agent.commission!) }}</p>
                                        <ValueChange
                                            :value="agent.commission_change!"
                                            :change="euroDisplay(agent.commission_change!)"
                                        />
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-3 py-5 text-gray-500">
                                    <div class="flex items-center">
                                        <p class="text-900 text-sm">{{ quotaDisplay(agent.quota_attainment!) }}</p>
                                        <ValueChange
                                            :value="agent.quota_attainment_change!"
                                            :change="roundFloat(agent.quota_attainment_change! * 100) + '%'"
                                        />
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500">
                                    <div class="flex items-center gap-1">
                                        <span class="font-semibold text-gray-600">{{
                                            agent.sick_leaves_days_count!
                                        }}</span>
                                        days sick

                                        <PencilSquareIcon
                                            class="ml-1 h-3 w-3 cursor-pointer hover:text-black"
                                            @click="$emit('open-paid-leave-slide-over', agent.id, 'sick')"
                                        />
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="font-semibold text-gray-600">{{
                                            agent.vacation_leaves_days_count!
                                        }}</span>
                                        days on vacation

                                        <PencilSquareIcon
                                            class="ml-1 h-3 w-3 cursor-pointer hover:text-black"
                                            @click="$emit('open-paid-leave-slide-over', agent.id, 'on vacation')"
                                        />
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </Card>
</template>
