<script setup lang="ts">
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue';
import { subtleText } from '../styles'
const props = defineProps<{
    text: string
}>()
</script>

<template>
    <div class="mb-5 flex items-baseline justify-between">
        <p :class="subtleText">{{ props.text }}</p>
        <PrimaryButton text="Details" />
    </div>
</template>
