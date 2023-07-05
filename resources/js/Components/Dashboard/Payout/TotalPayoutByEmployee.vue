<script setup lang="ts">
import Card from '@/Components/Card.vue'
import Agent from '@/types/Agent';

const props = defineProps<{
    agents: Array<Agent>
}>()

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
                                v-for="person in props.agents"
                                :key="person.email"
                            >
                                <td class="whitespace-nowrap py-5 pl-4 pr-3 text-sm sm:pl-0">
                                    <div class="flex items-center">
                                        <div class="h-11 w-11 flex-shrink-0">
                                            <img
                                                class="h-11 w-11 rounded-full"
                                                :src="person.image"
                                                alt=""
                                            />
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-medium text-gray-900">{{ person.name }}</div>
                                            <div class="mt-1 text-gray-500">{{ person.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500">
                                    <div class="text-gray-900">{{ person.title }}</div>
                                    <div class="mt-1 text-gray-500">{{ person.department }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500">
                                    <span
                                        class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20"
                                        >Active</span
                                    >
                                </td>
                                <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500">{{ person.role }}</td>
                                <td
                                    class="relative whitespace-nowrap py-5 pl-3 pr-4 text-right text-sm font-medium sm:pr-0"
                                >
                                    <a
                                        href="#"
                                        class="text-indigo-600 hover:text-indigo-900"
                                        >Edit<span class="sr-only">, {{ person.name }}</span></a
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </Card>
</template>
