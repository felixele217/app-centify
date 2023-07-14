<script setup lang="ts">
import Button from './Button.vue'
const props = withDefaults(
    defineProps<{
        type?: 'button' | 'submit' | 'reset'
        text?: string
    }>(),
    {
        type: 'button',
    }
)
</script>

<template>
    <Button
        type="button"
        padding="px-2.5 py-1.5"
        class="bg-white px text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-30 disabled:hover:bg-white"
        :text="props.text"
    >
        <slot />
    </Button>
</template>
