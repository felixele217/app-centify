<script setup lang="ts">
import Navigation from '@/Components/Deal/Index/Navigation.vue'
import ThumbsDownIcon from '@/Components/Icon/ThumbsDownIcon.vue'
import ThumbsUpIcon from '@/Components/Icon/ThumbsUpIcon.vue'
import Modal from '@/Components/Modal.vue'
import PageHeader from '@/Components/PageHeader.vue'
import Table from '@/Components/Table.vue'
import Deal from '@/types/Deal'
import { DealScopeEnum } from '@/types/Enum/DealScopeEnum'
import euroDisplay from '@/utils/euroDisplay'
import notify from '@/utils/notify'
import queryParamValue from '@/utils/queryParamValue'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps<{
    deals: Array<Deal>
}>()

function acceptDeal(dealId: number) {
    router.put(
        route('deals.update', dealId),
        {
            has_accepted_deal: true,
        },
        {
            onSuccess: () => {
                dealIdBeingAccepted.value = null
                notify('Deal accepted!', "It now counts towards this agent's commission metrics.")
            },
        }
    )
}

const dealIdBeingAccepted = ref<number | null>()

const noDealsText = computed(() => {
    if (props.deals.length > 0) {
        return ''
    }

    switch (queryParamValue('scope') as DealScopeEnum) {
        case 'open':
            return 'Open Text'
        case 'accepted':
            return 'Accepted Text'
        case 'declined':
            return 'Declined Text'
    }
})
</script>

<template>
    <div>
        <div class="flex justify-between">
            <page-header
                class="mb-0"
                title="To-Dos"
                description="All opportunities that need to be reviewed."
                no-bottom-margin
            />

            <Navigation />
        </div>

        <Table :no-items-text="noDealsText">
            <template #header>
                <tr>
                    <th
                        scope="col"
                        class="py-3.5 pl-6 pr-3 text-left text-sm font-semibold text-gray-900"
                    >
                        Owner
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
                        Description
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
            </template>
            <template #body>
                <tr v-for="deal in props.deals">
                    <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm">
                        <p class="text-gray-900">{{ deal.agent!.name }}</p>
                        <p class="mt-1 text-gray-500">{{ deal.agent!.email }}</p>
                    </td>

                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                        <a
                            class="link"
                            :href="`https://paul-sandbox11.pipedrive.com/deal/${deal.integration_deal_id}`"
                            target="_blank"
                        >
                            {{ deal.title }}
                        </a>
                    </td>

                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">description...</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        {{ euroDisplay(deal.value) }}
                    </td>

                    <td class="whitespace-nowrap px-3">
                        <div class="flex gap-2 text-gray-500">
                            <thumbs-up-icon
                                @click="dealIdBeingAccepted = deal.id"
                                class="h-6 w-6 cursor-pointer hover:text-green-500"
                            />
                            <thumbs-down-icon class="h-6 w-6 cursor-pointer hover:text-red-600" />
                        </div>
                    </td>
                </tr>
            </template>
        </Table>

        <Modal
            @modal-action="acceptDeal(dealIdBeingAccepted!)"
            :is-negative-action="false"
            :isOpen="!!dealIdBeingAccepted"
            @close-modal="dealIdBeingAccepted = null"
            button-text="Accept"
            title="Accept Deal"
            :description="'Are you sure you want to accept this deal? \nThis will affect the agent\'s quota and commission and is currently irreversible.'"
        />
    </div>
</template>
