<script setup lang="ts">
const props = defineProps<{
    heading: string
    description: string
}>()
</script>

<template>
    <div class="space-y-4 border-t border-gray-200 pt-5">
        <div class="mb-5">
            <h4 class="font-semibold">{{ props.heading }}</h4>
            <p class="mt-1 text-sm">{{ props.description }}</p>
        </div>
        <slot />
    </div>
</template>
