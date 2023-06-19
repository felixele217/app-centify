<script setup lang="ts">
import PipedriveLogo from '@/Components/Logos/PipedriveLogo.vue'
import SalesforceLogo from '@/Components/Logos/SalesforceLogo.vue'
import { router } from '@inertiajs/vue3'
import PrimaryButton from '../Buttons/PrimaryButton.vue'

const props = defineProps<{
    for: 'pipedrive' | 'salesforce'
}>()

function testIntegration() {
    router.get(
        route(`${props.for}.test`),
        {},
        {
            onSuccess: () => alert('Test successful'),
            onError: () => alert('Not yet implemented'),
        }
    )
}
</script>

<template>
    <div class="flex items-center justify-between gap-5 rounded-md bg-white px-10 py-4">
        <div class="flex items-center gap-5">
            <PipedriveLogo v-if="props.for === 'pipedrive'" />
            <SalesforceLogo v-if="props.for === 'salesforce'" />

            <h3>{{ props.for }}</h3>
        </div>

        <PrimaryButton
            text="Test"
            @click="testIntegration"
        />
    </div>
</template>
