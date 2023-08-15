<script setup lang="ts">
import { IntegrationTypeEnum } from '@/types/Enum/IntegrationTypeEnum'
import Integration from '@/types/Integration'
import hasMissingCustomField from '@/utils/Integration/hasMissingCustomField'
import { Cog6ToothIcon } from '@heroicons/vue/24/outline'
import { ExclamationCircleIcon } from '@heroicons/vue/24/solid'
import { router } from '@inertiajs/vue3'
import Card from '../Card.vue'
import IntegrationLogo from '../Logos/IntegrationLogo.vue'
import Tooltip from '../Tooltip.vue'
import SyncOrConnectIntegration from './SyncOrConnectIntegration.vue'

const props = defineProps<{
    integrationName: IntegrationTypeEnum
    activeIntegration: Integration | null
}>()
</script>

<template>
    <Card class="relative w-80">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <IntegrationLogo :integrationName="props.integrationName" />
                <h3>{{ props.integrationName }}</h3>
            </div>
            <div>
                <Tooltip
                    :text="
                        hasMissingCustomField(props.activeIntegration)
                            ? 'You did not configure all of the required fields to sync your integration data yet. To do so, please click the wheel and follow the instructions.'
                            : ''
                    "
                >
                    <Cog6ToothIcon
                        @click="router.get(route('integrations.custom-fields.index', props.activeIntegration.id))"
                        class="-mb-1.5 h-6 w-6 cursor-pointer hover:text-primary"
                        v-if="props.activeIntegration"
                    />

                    <ExclamationCircleIcon
                        v-if="hasMissingCustomField(props.activeIntegration)"
                        class="absolute right-3 top-2 h-5 w-5 text-red-500"
                    />
                </Tooltip>
            </div>
        </div>

        <div class="mt-10">
            <SyncOrConnectIntegration
                :integration-name="props.integrationName"
                :active-integration="props.activeIntegration"
            />
        </div>
    </Card>
</template>
