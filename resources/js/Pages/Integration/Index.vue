<script setup lang="ts">
import IntegrationCard from '@/Components/Integrations/IntegrationCard.vue'
import { IntegrationTypeEnum } from '@/types/Enum/IntegrationTypeEnum'
import { Head } from '@inertiajs/vue3'

const availableIntegrations: Array<IntegrationTypeEnum> = ['pipedrive']
</script>

<template>
    <Head title="Integrations" />
    <div class="flex gap-5">
        <IntegrationCard
            v-for="availableIntegration in availableIntegrations"
            :integration="availableIntegration"
        />
    </div>
</template>
