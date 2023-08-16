<script setup lang="ts">
import Integration from '@/types/Integration'
import hasMissingCustomField from '@/utils/Integration/hasMissingCustomField'
import LastSynced from './LastSynced.vue'
import SyncIntegrationButton from './SyncIntegrationButton.vue'

const props = defineProps<{
    integrationName: 'pipedrive'
    activeIntegration: Integration
}>()
console.log(props.activeIntegration)
</script>

<template>
    <div
        class="flex items-center justify-between gap-5"
        v-if="props.activeIntegration"
    >
        <LastSynced :last-synced="props.activeIntegration.last_synced_at ? new Date(props.activeIntegration.last_synced_at) : null" />

        <SyncIntegrationButton
            :disabled="hasMissingCustomField(props.activeIntegration)"
            :integrationName="props.integrationName"
        />
    </div>
</template>
