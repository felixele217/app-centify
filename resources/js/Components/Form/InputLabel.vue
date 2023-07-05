<script setup lang="ts">
const props = defineProps<{
    value?: string
    required?: boolean
}>()
</script>

<template>
    <div class="flex">
        <label class="block text-sm font-medium text-gray-700">
            <span v-if="value">{{ value }}</span>
            <span v-else><slot /></span>
        </label>

        <p
            class="text-sm text-red-500"
            v-if="props.required"
        >
            *
        </p>
    </div>
</template>
