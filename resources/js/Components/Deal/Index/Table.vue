<script setup lang="ts">
import Navigation from '@/Components/Deal/Index/Navigation.vue'
import Modal from '@/Components/Modal.vue'
import PageHeader from '@/Components/PageHeader.vue'
import TableWrapper from '@/Components/TableWrapper.vue'
import Deal from '@/types/Deal'
import { DealScopeEnum } from '@/types/Enum/DealScopeEnum'
import Integration from '@/types/Integration'
import paymentCycle from '@/utils/Date/paymentCycle'
import euroDisplay from '@/utils/euroDisplay'
import notify from '@/utils/notify'
import queryParamValue from '@/utils/queryParamValue'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import DealStatus from './DealStatus.vue'

const props = defineProps<{
    deals: Array<Deal>
    integrations: Array<Integration>
}>()

function updateDeal() {
    router.put(
        route('deals.update', (dealIdBeingAccepted.value || dealIdBeingDeclined.value)!),
        {
            has_accepted_deal: !!dealIdBeingAccepted.value,
        },
        {
            onSuccess: () => {
                notifyUpdate()
                dealIdBeingAccepted.value = null
                dealIdBeingDeclined.value = null
            },
        }
    )
}

const pipedriveSubdomain = computed(() => props.integrations[0].subdomain)

function notifyUpdate() {
    const title = dealIdBeingAccepted.value ? 'Deal accepted!' : 'Deal declined!'
    const description = dealIdBeingAccepted.value
        ? "It now counts towards this agent's commission metrics."
        : "This deal will not affect this agent's commission metrics."

    notify(title, description)
}

const dealIdBeingAccepted = ref<number | null>()
const dealIdBeingDeclined = ref<number | null>()

const noDealsText = computed(() => {
    if (props.deals.length > 0) {
        return ''
    }

    switch (queryParamValue('scope') as DealScopeEnum | '') {
        case '':
            return 'You have no deals yet. You might want to sync your integrations to load your deals into centify.'
        case 'open':
            return 'You do not have any open deals currently.'
        case 'accepted':
            return 'You do not have any accepted deals yet.'
        case 'declined':
            return 'You do not have any declined deals yet.'
    }
})

const dealsText = computed(() => {
    switch (queryParamValue('scope') as DealScopeEnum | '') {
        case '':
            return 'All of your deals.'
        case 'open':
            return 'All open deals.'
        case 'accepted':
            return 'All accepted deals.'
        case 'declined':
            return 'All rejected deals.'
    }
})
</script>

<template>
    <div>
        <div class="flex justify-between">
            <page-header
                class="mb-2"
                title="Deals"
                :description="dealsText"
                no-bottom-margin
            />

            <Navigation />
        </div>

        <TableWrapper :no-items-text="noDealsText">
            <template #header>
                <tr class="grid grid-cols-12 items-center">
                    <th
                        scope="col"
                        class="col-span-3 py-3.5 pl-6 pr-3 text-left text-sm font-semibold text-gray-900"
                    >
                        Owner
                    </th>
                    <th
                        scope="col"
                        class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                    >
                        Name
                    </th>
                    <th
                        scope="col"
                        class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                    >
                        Deal Value
                    </th>
                    <th
                        scope="col"
                        class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                    >
                        Payment Cycle
                    </th>
                    <th
                        scope="col"
                        class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                    >
                        Notes
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
                <tr
                    v-for="deal in props.deals"
                    class="grid grid-cols-12 items-center whitespace-nowrap text-sm"
                >
                    <td class="col-span-3 py-4 pl-6 pr-3">
                        <p class="text-gray-900">{{ deal.agent!.name }}</p>
                        <p class="mt-1 text-gray-500">{{ deal.agent!.email }}</p>
                    </td>

                    <td class="col-span-2 px-3 py-4">
                        <a
                            class="link"
                            :href="`https://${pipedriveSubdomain}.pipedrive.com/deal/${deal.integration_deal_id}`"
                            target="_blank"
                        >
                            {{ deal.title }}
                        </a>
                    </td>

                    <td class="col-span-2 px-3 py-4 text-gray-500">
                        {{ euroDisplay(deal.value) }}
                    </td>

                    <td class="col-span-2 px-3 py-4 text-gray-500">
                        {{ paymentCycle(deal.add_time) }}
                    </td>

                    <td class="col-span-2 px-3 py-4 text-gray-500">notes...</td>

                    <td class="px-3">
                        <DealStatus
                            :deal="deal"
                            @accepted="(id: number) => dealIdBeingAccepted = id"
                            @declined="(id: number) => dealIdBeingDeclined = id"
                        />
                    </td>
                </tr>
            </template>
        </TableWrapper>

        <Modal
            @modal-action="updateDeal"
            :isOpen="!!dealIdBeingAccepted"
            @close-modal="dealIdBeingAccepted = null"
            button-text="Accept"
            title="Accept Deal"
            :description="'Are you sure you want to accept this deal? \nThis will affect the agent\'s quota and commission and is currently irreversible.'"
        />

        <Modal
            @modal-action="updateDeal"
            is-negative-action
            :isOpen="!!dealIdBeingDeclined"
            @close-modal="dealIdBeingDeclined = null"
            button-text="Decline"
            title="Decline Deal"
            :description="'Are you sure you want to decline this deal? \nThis will affect the agent\'s quota and commission and is currently irreversible.'"
        />
    </div>
</template>
