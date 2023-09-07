<script setup lang="ts">
import Button from './Button.vue'

const props = defineProps<{
    text?: string
    padding?: string
    disabled?: boolean
    processing?: boolean
}>()
</script>

<template>
    <Button
        :is-processing="props.processing"
        :padding="props.padding"
        :text="props.text"
        :class="`${
            props.disabled ? 'bg-gray-300 hover:bg-gray-300' : 'bg-primary-500 hover:bg-primary-400'
        } text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-400`"
        :disabled="props.disabled || props.processing"
    >
        <slot />
    </Button>
</template>
