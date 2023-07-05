<script setup lang="ts">
import PipedriveLogo from '@/Components/Logos/PipedriveLogo.vue'
import SalesforceLogo from '@/Components/Logos/SalesforceLogo.vue'
import { router, usePage } from '@inertiajs/vue3'
import PrimaryButton from '../Buttons/PrimaryButton.vue'
import Card from '../Card.vue'
import Admin from '@/types/Admin'

const props = defineProps<{
    for: 'pipedrive' | 'salesforce'
}>()

const activeIntegrations = (usePage().props.auth.user as Admin).active_integrations

const authenticate = () => (window.location.href = route(`authenticate.${props.for}.create`))

function syncIntegration() {
    router.get(
        route(`${props.for}.sync`),
        {},
        {
            onSuccess: () => alert('Synchronization successful'),
            onError: () => alert('Not yet implemented'),
        }
    )
}
</script>

<template>
    <Card class="w-60">
        <div class="flex items-center gap-4">
            <PipedriveLogo v-if="props.for === 'pipedrive'" />
            <SalesforceLogo v-if="props.for === 'salesforce'" />
            <h3>{{ props.for }}</h3>
        </div>

        <div class="mt-5">
            <div
                class="flex items-center justify-between"
                v-if="!activeIntegrations[props.for]"
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
            <div
                class="flex items-center justify-between gap-5"
                v-if="activeIntegrations[props.for]"
            >
                <div class="-mb-1 flex items-center gap-3">
                    <div class="h-2 w-2 rounded-full bg-green-500 ring-4 ring-green-100" />
                    <p class="-mt-0.5 text-sm font-semibold">active</p>
                </div>
                <PrimaryButton
                    text="Sync"
                    @click="syncIntegration"
                />
            </div>
        </div>
    </Card>
</template>
