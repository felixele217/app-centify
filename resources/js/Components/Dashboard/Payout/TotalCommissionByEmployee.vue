<script setup lang="ts">
import ActivePlans from '@/Components/Agent/ActivePlans.vue'
import AgentNameColumn from '@/Components/AgentNameColumn.vue'
import Card from '@/Components/Card.vue'
import Filter from '@/Components/Form/Filter.vue'
import PageHeader from '@/Components/PageHeader.vue'
import HideInProduction from '@/Components/System/HideInProduction.vue'
import Agent from '@/types/Agent'
import { AgentStatusEnum } from '@/types/Enum/AgentStatusEnum'
import Plan from '@/types/Plan/Plan'
import euroDisplay from '@/utils/euroDisplay'
import roundFloat from '@/utils/roundFloat'
import { FolderArrowDownIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import ValueChange from './ValueChange.vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps<{
    agents: Array<Agent>
    plans: Array<Pick<Plan, 'id' | 'name'>>
}>()

defineEmits<{
    'open-paid-leave-slide-over': [agentId: number, type: AgentStatusEnum]
}>()
</script>

<template>
    <Card>
        <PageHeader
            title="Total Commission by Employee"
            description="Overview of your agents' performances."
        >
            <template #custom-button>
                <HideInProduction class="mr-10">
                    <a
                        class="rounded-full bg-primary-50 p-2 hover:bg-primary-100"
                        :href="route('payouts-export')"
                    >
                        <FolderArrowDownIcon class="h-5 w-5" />
                    </a>
                </HideInProduction>
                <Filter :reload-url="route('dashboard')" />
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
                                    <AgentNameColumn
                                        :agent="agent"
                                        v-if="usePage().props.environment === 'production'"
                                    />

                                    <HideInProduction>
                                        <AgentNameColumn
                                            :agent="agent"
                                            class="cursor-pointer"
                                            @click="router.get(route('agents.show', agent.id))"
                                        />
                                    </HideInProduction>
                                </td>

                                <td class="whitespace-pre-wrap py-5 pl-4 pr-3 text-sm text-gray-500 sm:pl-0">
                                    <ActivePlans
                                        :plans="props.plans"
                                        :agent="agent"
                                    />
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
                                        <p class="text-900 text-sm">{{ agent.quota_attainment_in_percent! }}%</p>
                                        <ValueChange
                                            :value="agent.quota_attainment_change!"
                                            :change="roundFloat(agent.quota_attainment_change! * 100) + '%'"
                                        />
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500">
                                    <div
                                        class="flex cursor-pointer items-center gap-1 hover:text-black"
                                        @click="$emit('open-paid-leave-slide-over', agent.id, 'sick')"
                                    >
                                        <span class="font-semibold text-gray-600">{{
                                            agent.sick_leaves_days_count!
                                        }}</span>
                                        days sick

                                        <div
                                            class="ml-1 h-3 w-3"
                                            dusk="manage-paid-leaves-slide-over-button"
                                        >
                                            <PencilSquareIcon />
                                        </div>
                                    </div>
                                    <div
                                        class="flex cursor-pointer items-center gap-1 hover:text-black"
                                        @click="$emit('open-paid-leave-slide-over', agent.id, 'on vacation')"
                                    >
                                        <span class="font-semibold text-gray-600">{{
                                            agent.vacation_leaves_days_count!
                                        }}</span>
                                        days on vacation

                                        <div class="ml-1 h-3 w-3">
                                            <PencilSquareIcon />
                                        </div>
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
