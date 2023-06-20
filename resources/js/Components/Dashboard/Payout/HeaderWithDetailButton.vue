<script setup lang="ts">
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue'
const props = defineProps<{
    text: string
}>()
</script>

<template>
    <div class="flex items-baseline justify-between">
        <p class="font-semibold">{{ props.text }}</p>
        <PrimaryButton text="Details" />
    </div>
</template>
