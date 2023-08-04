<script setup lang="ts">
import IntegrationCard from '@/Components/Integrations/IntegrationCard.vue'
import { IntegrationTypeEnum } from '@/types/Enum/IntegrationTypeEnum'
import Integration from '@/types/Integration'
import { Head } from '@inertiajs/vue3'

const props = defineProps<{
    integrations: Array<Integration | null>
    activeIntegrations: Record<IntegrationTypeEnum, string> | null
}>()

const availableIntegrationNames: Array<IntegrationTypeEnum> = ['pipedrive']

function isActive(availableIntegrationName: IntegrationTypeEnum) {
    if (!props.integrations.length) {
        return false
    }

    return props.integrations.filter((integration) => integration!.name === availableIntegrationName)[0]
}

function hasAllCustomFields(availableIntegration: IntegrationTypeEnum) {
    return true
}
</script>

<template>
    <Head title="Integrations" />
    <div class="flex gap-5">
        <IntegrationCard
            v-for="availableIntegrationName in availableIntegrationNames"
            :integrationName="availableIntegrationName"
            :isActive="isActive(availableIntegrationName)"
            :has-all-custom-fields="hasAllCustomFields(availableIntegrationName)"
        />
    </div>
</template>
