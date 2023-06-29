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
        :class="`bg-indigo-600 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600`"
    >
        <slot />
    </Button>
</template>
