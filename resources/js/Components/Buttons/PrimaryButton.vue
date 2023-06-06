<script setup lang="ts">
import Button from './Button.vue'

const props = defineProps<{
    text?: string
    padding?: string
}>()
</script>

<template>
    <Button
        :padding="props.padding"
        :text="props.text"
        class="bg-gray-800 text-white hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800"
    >
        <slot />
    </Button>
</template>
