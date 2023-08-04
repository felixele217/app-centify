<script setup lang="ts">
import IntegrationCard from '@/Components/Integrations/IntegrationCard.vue'
import { IntegrationTypeEnum } from '@/types/Enum/IntegrationTypeEnum'
import { Head } from '@inertiajs/vue3'

const props = defineProps<{
    activeIntegrations: Record<IntegrationTypeEnum, string> | null
}>()

const availableIntegrations: Array<IntegrationTypeEnum> = ['pipedrive']
console.log(props.activeIntegrations)

function isActive(availableIntegration: IntegrationTypeEnum) {
    if (!props.activeIntegrations) {
        return false
    }

    return Object.keys(props.activeIntegrations).includes(availableIntegration)
}
</script>

<template>
    <Head title="Integrations" />
    <div class="flex gap-5">
        <IntegrationCard
            v-for="availableIntegration in availableIntegrations"
            :integrationName="availableIntegration"
            :isActive="isActive(availableIntegration)"
        />
    </div>
</template>
