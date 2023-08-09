<script setup lang="ts">
import Button from './Button.vue'

const props = defineProps<{
    text?: string
    padding?: string
    disabled?: boolean
}>()
</script>

<template>
    <Button
        :padding="props.padding"
        :text="props.text"
        :class="`${
            props.disabled ? 'bg-gray-300 hover:bg-gray-300' : 'bg-primary hover:bg-primary-hover'
        } text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-hover`"
        :disabled="props.disabled"
    >
        <slot />
    </Button>
</template>
