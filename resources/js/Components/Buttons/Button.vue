<script setup lang="ts">
const props = defineProps<{
    text?: string
    padding?: string
    disabled?: boolean
    isProcessing?: boolean
}>()
</script>

<template>
    <button
        class="flex items-center rounded-md text-center text-sm font-semibold transition duration-150 ease-in-out"
        :class="`${props.padding ? props.padding : 'px-4 py-2'}`"
        :disabled="props.disabled"
    >
        <svg
            v-if="props.isProcessing"
            class="-ml-1 mr-2 h-5 w-5 animate-spin text-white"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
            ></circle>
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
        </svg>

        <p v-if="props.text">{{ props.text }}</p>
        <slot v-else />
    </button>
</template>
