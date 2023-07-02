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
        class="border border-gray-300 bg-white text-gray-900 shadow-sm hover:bg-gray-50"
        :text="props.text"
    >
        <slot />
    </Button>
</template>
