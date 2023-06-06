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
        class="border-gray-800 text-gray-800 hover:text-gray-700 hover:border-gray-700 focus:border-gray-700 active:border-gray-800"
        :text="props.text"
    >
        <slot />
    </Button>
</template>
