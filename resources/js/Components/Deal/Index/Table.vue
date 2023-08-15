<script setup lang="ts">
import Navigation from '@/Components/Deal/Index/Navigation.vue'
import SyncOrConnectIntegration from '@/Components/Integrations/SyncOrConnectIntegration.vue'
import PageHeader from '@/Components/PageHeader.vue'
import TableWrapper from '@/Components/TableWrapper.vue'
import Deal from '@/types/Deal'
import { DealScopeEnum } from '@/types/Enum/DealScopeEnum'
import Integration from '@/types/Integration'
import queryParamValue from '@/utils/queryParamValue'
import { computed } from 'vue'
import DealCard from './DealCard.vue'

const props = defineProps<{
    deals: Array<Deal>
    integrations: Array<Integration>
}>()

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
        case 'rejected':
            return 'You do not have any rejected deals yet.'
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
        case 'rejected':
            return 'All rejected deals.'
    }
})

const pipedriveIntegration = computed(() => {
    return props.integrations.filter((integration) => integration.name === 'pipedrive')[0]
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

            <div class="flex items-center gap-10 pb-5">
                <div class="flex items-end gap-5">
                    <SyncOrConnectIntegration
                        integration-name="pipedrive"
                        :active-integration="pipedriveIntegration"
                    />
                </div>

                <Navigation />
            </div>
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
                        Attribution Period
                    </th>
                    <th
                        scope="col"
                        class="col-span-4 px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
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
                    <DealCard
                        :deal="deal"
                        :integrations="props.integrations"
                    />
                </tr>
            </template>
        </TableWrapper>
    </div>
</template>
