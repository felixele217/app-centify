<script setup lang="ts">
const props = defineProps<{
    text?: string
    padding?: string
    disabled?: boolean
}>()
</script>

<template>
    <button
        class="inline-flex items-center rounded-md text-center text-sm font-semibold transition duration-150 ease-in-out"
        :class="`${props.padding ? props.padding : 'px-4 py-2'}`"
        :disabled="props.disabled"
    >
        <p v-if="props.text">{{ props.text }}</p>
        <slot v-else />
    </button>
</template>
