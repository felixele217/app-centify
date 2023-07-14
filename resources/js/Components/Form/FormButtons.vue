<script setup lang="ts">
import PrimaryButton from '../Buttons/PrimaryButton.vue'
import SecondaryButton from '../Buttons/SecondaryButton.vue'

const props = defineProps<{
    positiveButtonText: string
}>()
</script>

<template>
    <div class="flex flex-shrink-0 justify-end gap-3 py-4">
        <SecondaryButton
            text="Cancel"
            @click.prevent="$emit('cancel-button-clicked')"
        />

        <PrimaryButton
            :text="props.positiveButtonText"
            @click="$emit('create-button-clicked')"
        />
    </div>
</template>
