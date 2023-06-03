<script setup lang="ts">
const props = defineProps<{
    text?: string
    padding?: string
}>()
</script>

<template>
    <button :class="props.padding ? props.padding : 'px-4 py-2'">
        <p v-if="props.text">{{ props.text }}</p>
        <slot v-else />
    </button>
</template>
