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
        class="bg-white text-gray-900 shadow-sm border-slate-300 hover:bg-gray-50"
        :text="props.text"
    >
        <slot />
    </Button>
</template>
