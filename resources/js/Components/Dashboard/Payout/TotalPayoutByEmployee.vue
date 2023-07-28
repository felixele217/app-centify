<script setup lang="ts">
import Card from '@/Components/Card.vue'
import Filter from '@/Components/Form/Filter.vue'
import PageHeader from '@/Components/PageHeader.vue'
import Agent from '@/types/Agent'
import euroDisplay from '@/utils/euroDisplay'
import roundFloat from '@/utils/roundFloat'
import ValueChange from './ValueChange.vue'

const props = defineProps<{
    agents: Array<Agent>
}>()

function quotaDisplay(quotaAttainment: number) {
    return roundFloat(quotaAttainment * 100) + '%'
}

const src =
    'https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80'
</script>

<template>
    <Card>
        <PageHeader
            title="Total Payout by Employee"
            description="Overview of your agents' performances."
        >
            <template #custom-button>
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
                            <tr
                                v-for="agent in props.agents"
                                :key="agent.email"
                            >
                                <td class="flex gap-5 whitespace-nowrap py-5 pl-4 pr-3 text-sm sm:pl-0">
                                    <div class="h-11 w-11 flex-shrink-0">
                                        <img
                                            class="h-11 w-11 rounded-full"
                                            :src="src"
                                            alt="Profile Photo"
                                        />
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ agent.name }}</div>
                                        <div class="mt-1 text-gray-500">{{ agent.email }}</div>
                                    </div>
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
                                    <div>
                                        <span class="font-semibold text-gray-600">{{
                                            agent.sick_leaves_days_count!
                                        }}</span>
                                        days sick
                                    </div>
                                    <div>
                                        <span class="font-semibold text-gray-600">{{
                                            agent.vacation_leaves_days_count!
                                        }}</span>
                                        days on vacation
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
