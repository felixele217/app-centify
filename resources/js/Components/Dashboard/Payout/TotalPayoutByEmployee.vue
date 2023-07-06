<script setup lang="ts">
import Card from '@/Components/Card.vue'
import Agent from '@/types/Agent'
import euroDisplay from '@/utils/euroDisplay'

const props = defineProps<{
    agents: Array<Agent>
}>()

function quotaDisplay(quotaAttainment: number) {
    return quotaAttainment * 100 + '%'
}
</script>

<template>
    <Card>
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-base font-semibold leading-6 text-gray-900">Total Payout by Employee</h1>
                <p class="mt-2 text-sm text-gray-700">Gain a quick overview over your Agents' Performances.</p>
            </div>
        </div>
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr
                                v-for="agent in props.agents"
                                :key="agent.email"
                            >
                                <td class="whitespace-nowrap py-5 pl-4 pr-3 text-sm sm:pl-0">
                                    <div class="font-medium text-gray-900">{{ agent.name }}</div>
                                    <div class="mt-1 text-gray-500">{{ agent.email }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500">
                                    <div class="text-gray-900">{{ euroDisplay(agent.commission!) }}</div>
                                    <div class="mt-1 text-gray-500">Change last month/quarter</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500">
                                    <div class="text-gray-900">{{ quotaDisplay(agent.quota_attainment!) }}</div>
                                    <div class="mt-1 text-gray-500">Change last month/quarter</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </Card>
</template>
