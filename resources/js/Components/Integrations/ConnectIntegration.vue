<script setup lang="ts">
import PrimaryButton from '../Buttons/PrimaryButton.vue'

const props = defineProps<{
    integrationName: string
}>()

const authenticate = () => (window.location.href = route(`authenticate.${props.integrationName}.create`))
</script>

<template>
    <div class="flex items-center justify-between">
        <PrimaryButton
            text="Connect"
            @click="authenticate()"
        />
    </div>
</template>
