<script setup lang="ts">
import PrimaryButton from '../Buttons/PrimaryButton.vue'
import SecondaryButton from '../Buttons/SecondaryButton.vue';
</script>

<template>
    <div class="flex flex-shrink-0 justify-end gap-3 py-4">
        <SecondaryButton
            text="Cancel"
            @click="$emit('cancel-button-clicked')"
        />

        <PrimaryButton
            text="Create"
            @click="$emit('create-button-clicked')"
        />
    </div>
</template>
