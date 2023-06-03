<script setup lang="ts">
const props = defineProps<{
    text?: string
}>()
</script>

<template>
    <button>
        <p v-if="props.text">{{ props.text }}</p>
        <slot v-else />
    </button>
</template>
