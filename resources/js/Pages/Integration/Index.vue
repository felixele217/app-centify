<script setup lang="ts">
import IntegrationCard from '@/Components/Integrations/IntegrationCard.vue'
import { IntegrationTypeEnum } from '@/types/Enum/IntegrationTypeEnum'
import Integration from '@/types/Integration'
import { Head } from '@inertiajs/vue3'

const props = defineProps<{
    integrations: Array<Integration | null>
}>()

const availableIntegrationNames: Array<IntegrationTypeEnum> = ['pipedrive']

function hasAllCustomFields(availableIntegration: IntegrationTypeEnum) {
    return true
}

function activeIntegration(availableIntegrationName: IntegrationTypeEnum) {
    return props.integrations.filter((integration) => integration!.name === availableIntegrationName)[0]
}
</script>

<template>
    <Head title="Integrations" />
    <div class="flex gap-5">
        <IntegrationCard
            v-for="availableIntegrationName in availableIntegrationNames"
            :integrationName="availableIntegrationName"
            :activeIntegration="activeIntegration(availableIntegrationName)"
        />
    </div>
</template>
