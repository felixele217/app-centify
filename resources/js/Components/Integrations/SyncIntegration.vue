<script setup lang="ts">
import Integration from '@/types/Integration'
import hasMissingCustomField from '@/utils/Integration/hasMissingCustomField'
import SyncIntegrationButton from './SyncIntegrationButton.vue'

const props = defineProps<{
    integrationName: 'pipedrive'
    activeIntegration: Integration
}>()
</script>

<template>
    <div
        class="flex items-end justify-between gap-5"
        v-if="props.activeIntegration"
    >
        <div class="flex items-center gap-3">
            <div class="h-2 w-2 rounded-full bg-green-500 ring-4 ring-green-100" />
            <p class="-mt-0.5 text-sm font-semibold">active</p>
        </div>

        <LastSynced :last-synced="new Date(props.activeIntegration.last_synced_at)" />

        <SyncIntegrationButton
            :disabled="hasMissingCustomField(props.activeIntegration)"
            :integrationName="props.integrationName"
        />
    </div>
</template>
