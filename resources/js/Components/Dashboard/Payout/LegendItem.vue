<script setup lang="ts">
const props = defineProps<{
    name: string
    color: string
}>()
</script>

<template>
    <div class="flex items-center gap-1">
        <div
            class="h-2 w-2 rounded-full"
            :class="`bg-${props.color}`"
        />
        <p class="text-sm text-gray-800">{{ props.name }}</p>
    </div>
</template>
