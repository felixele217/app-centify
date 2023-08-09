use App\Models\Integration;
<script setup lang="ts">
import { IntegrationTypeEnum } from '@/types/Enum/IntegrationTypeEnum'
import Integration from '@/types/Integration'
import { Cog6ToothIcon } from '@heroicons/vue/24/outline'
import { ExclamationCircleIcon } from '@heroicons/vue/24/solid'
import { router } from '@inertiajs/vue3'
import PrimaryButton from '../Buttons/PrimaryButton.vue'
import Card from '../Card.vue'
import IntegrationLogo from '../Logos/IntegrationLogo.vue'
import Tooltip from '../Tooltip.vue'
import SyncIntegrationButton from './SyncIntegrationButton.vue'

const props = defineProps<{
    integrationName: IntegrationTypeEnum
    activeIntegration: Integration | null
}>()

const authenticate = () => (window.location.href = route(`authenticate.${props.integrationName}.create`))

function hasMissingCustomField() {
    if (!props.activeIntegration) {
        return false
    }

    return props.activeIntegration!.custom_fields?.length !== 1
}
</script>

<template>
    <Card class="relative w-72">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <IntegrationLogo :integrationName="props.integrationName" />
                <h3>{{ props.integrationName }}</h3>
            </div>
            <div>
                <Tooltip
                    :text="
                        hasMissingCustomField()
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
                        v-if="hasMissingCustomField()"
                        class="absolute right-3 top-2 h-5 w-5 text-red-500"
                    />
                </Tooltip>
            </div>
        </div>

        <div class="mt-10">
            <div
                class="flex items-center justify-between gap-5"
                v-if="props.activeIntegration"
            >
                <div class="-mb-1 flex items-center gap-3">
                    <div class="h-2 w-2 rounded-full bg-green-500 ring-4 ring-green-100" />
                    <p class="-mt-0.5 text-sm font-semibold">active</p>
                </div>

                <SyncIntegrationButton
                    text="Sync"
                    :disabled="hasMissingCustomField()"
                    :integrationName="props.integrationName"
                />
            </div>

            <div
                class="flex items-center justify-between"
                v-else
            >
                <div class="-mb-1 flex items-center gap-2">
                    <div class="h-2 w-2 rounded-full bg-gray-300" />
                    <p class="-mt-0.5 text-sm">inactive</p>
                </div>

                <PrimaryButton
                    text="Connect"
                    @click="authenticate"
                />
            </div>
        </div>
    </Card>
</template>
