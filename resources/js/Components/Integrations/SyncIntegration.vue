<script setup lang="ts">
import PrimaryButton from '../Buttons/PrimaryButton.vue'

const props = defineProps<{
    integrationName: string
}>()

const authenticate = () => (window.location.href = route(`authenticate.${props.integrationName}.create`))
</script>

<template>
    <div class="flex items-center justify-between">
        <div class="-mb-1 flex items-center gap-2">
            <div class="h-2 w-2 rounded-full bg-gray-300" />
            <p class="-mt-0.5 text-sm">inactive</p>
        </div>

        <PrimaryButton
            text="Connect"
            @click="authenticate()"
        />
    </div>
</template>
