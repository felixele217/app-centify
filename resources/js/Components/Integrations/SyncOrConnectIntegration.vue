<script setup lang="ts">
import Integration from '@/types/Integration'
import ConnectIntegration from './ConnectIntegration.vue'
import SyncIntegration from './SyncIntegration.vue'

const props = defineProps<{
    integrationName: 'pipedrive'
    activeIntegration: Integration | null
    redirectUrl?: string
}>()
</script>

<template>
    <ConnectIntegration
        v-if="!props.activeIntegration"
        :integration-name="props.integrationName"
    />

    <SyncIntegration
        v-else
        :integration-name="props.integrationName"
        :active-integration="props.activeIntegration"
        :redirect-url="props.redirectUrl"
    />
</template>
