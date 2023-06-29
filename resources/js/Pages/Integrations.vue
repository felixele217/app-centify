<script setup lang="ts">
import IntegrationCard from '@/Components/Integrations/IntegrationCard.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
    <Head title="Integrations" />
    <div class="flex gap-5">
        <IntegrationCard for="pipedrive" />
        <!-- <IntegrationCard for="salesforce" /> -->
    </div>
</template>
