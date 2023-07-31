<script setup lang="ts">
import { IntegrationTypeEnum } from '@/types/Enum/IntegrationTypeEnum'
import notify from '@/utils/notify'
import { Cog6ToothIcon } from '@heroicons/vue/24/outline'
import { router, usePage } from '@inertiajs/vue3'
import PrimaryButton from '../Buttons/PrimaryButton.vue'
import Card from '../Card.vue'
import Logo from '../Logos/Logo.vue'

const props = defineProps<{
    for: IntegrationTypeEnum
}>()

const activeIntegrations = usePage().props.integrations as Record<IntegrationTypeEnum, boolean>

const authenticate = () => (window.location.href = route(`authenticate.${props.for}.create`))

function syncIntegration() {
    router.get(
        route(`${props.for}.sync`),
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
    <Card class="w-72">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Logo :for="props.for" />
                <h3>{{ props.for }}</h3>
            </div>

            <Cog6ToothIcon
                @click="router.get(route('custom-integration-fields.index'))"
                class="h-6 w-6 cursor-pointer hover:text-primary"
                v-if="activeIntegrations[props.for]"
            />
        </div>

        <div class="mt-10">
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

            <div
                class="flex items-center justify-between"
                v-else
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
        </div>
    </Card>
</template>
