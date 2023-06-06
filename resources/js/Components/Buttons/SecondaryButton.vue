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
        class="border-violet-700 text-violet-700 hover:text-violet-600 hover:border-violet-600 focus:border-violet-600 active:border-violet-700"
        :text="props.text"
    >
        <slot />
    </Button>
</template>
