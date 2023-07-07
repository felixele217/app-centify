<script setup lang="ts">
import ThumbsDownIcon from '@/Components/Icon/ThumbsDownIcon.vue'
import ThumbsUpIcon from '@/Components/Icon/ThumbsUpIcon.vue'
import PageHeader from '@/Components/PageHeader.vue';
import Deal from '@/types/Deal'
import euroDisplay from '@/utils/euroDisplay'

const props = defineProps<{
    deals: Array<Deal>
}>()
</script>

<template>
    <div>
        <page-header title="Todos" description="A list of all opportunities that require manual action." />
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"
                                    >
                                        Owner
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                    >
                                        Creator
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                    >
                                        Name
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                    >
                                        Pipedrive Notes
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                    >
                                        Deal Value
                                    </th>
                                    <th
                                        scope="col"
                                        class="relative py-3.5 pl-3 pr-4 sm:pr-6"
                                    >
                                        <span class="sr-only">Edit</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="deal in props.deals">
                                    <td class="whitespace-nowrap py-4 border-t pl-4 pr-3 text-sm text-gray-900 sm:pl-6">
                                        {{ deal.owner_email }}
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        <p class="text-gray-900">{{ deal.agent!.name }}</p>
                                        <p class="mt-1 text-gray-500">{{ deal.agent!.email }}</p>
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        <a
                                            class="link"
                                            :href="`https://paul-sandbox11.pipedrive.com/deal/${deal.integration_deal_id}`"
                                        >
                                            {{ deal.title }}
                                        </a>
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">pipedrive notes</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ euroDisplay(deal.value) }}
                                    </td>
                                    <td class="whitespace-nowrap px-3">
                                        <div class="flex gap-2 text-gray-500">
                                            <thumbs-up-icon class="h-6 w-6 cursor-pointer hover:text-green-500" />
                                            <thumbs-down-icon class="h-6 w-6 cursor-pointer hover:text-red-600" />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
