<script setup lang="ts">
import Button from './Button.vue'

const props = defineProps<{
    text?: string
    padding?: string
}>()
</script>

<template>
    <Button
        class="inline-flex items-center rounded-md border border-transparent bg-gray-800 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white dark:focus:bg-white dark:focus:ring-offset-gray-800 dark:active:bg-gray-300"
        :padding="props.padding"
        :text="props.text"
    >
        <slot />
    </Button>
</template>
