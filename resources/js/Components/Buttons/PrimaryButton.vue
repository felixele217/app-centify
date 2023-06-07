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
        :class="`bg-violet-primary text-white hover:bg-violet-600 focus:bg-violet-600 active:bg-violet-primary`"
    >
        <slot />
    </Button>
</template>
