<script setup lang="ts">
import { IntegrationTypeEnum } from '@/types/Enum/IntegrationTypeEnum'
import notify from '@/utils/notify'
import { router, usePage } from '@inertiajs/vue3'
import PrimaryButton from '../Buttons/PrimaryButton.vue'

const props = withDefaults(
    defineProps<{
        integrationName?: IntegrationTypeEnum
        redirectUrl: string
        text?: string
        disabled?: boolean
    }>(),
    {
        text: 'Sync',
        integrationName: 'pipedrive',
        redirectUrl: window.location.href,
        disabled: false,
    }
)

function syncIntegration() {
    router.get(
        route(`${props.integrationName}.sync`) + `?redirect_url=${props.redirectUrl}`,
        {},
        {
            onSuccess: () =>
                notify('Sychronization successful', 'Our application now uses your latest integration data.'),
            onError: () =>
                notify('Sychronization failed', usePage().props.errors[Object.keys(usePage().props.errors)[0]], false),
        }
    )
}
</script>

<template>
    <PrimaryButton
        :text="props.text"
        @click="syncIntegration"
        :disabled="props.disabled"
    />
</template>
