<script setup lang="ts">
import IntegrationCard from '@/Components/Integrations/IntegrationCard.vue'
import MainLayout from '@/Layouts/MainLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
</script>

<template>
    <Head title="Integrations" />
    <MainLayout>
        <div class="flex gap-5">
            <IntegrationCard for="pipedrive" />
            <IntegrationCard for="salesforce" />
        </div>
    </MainLayout>
</template>
