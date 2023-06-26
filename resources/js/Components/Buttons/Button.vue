<script setup lang="ts">
const props = defineProps<{
    text?: string
    padding?: string
}>()
</script>

<template>
    <button
        class="inline-flex items-center rounded-md border border-transparent text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        :class="props.padding ? props.padding : 'px-4 py-2'"
    >
        <p v-if="props.text">{{ props.text }}</p>
        <slot v-else />
    </button>
</template>
