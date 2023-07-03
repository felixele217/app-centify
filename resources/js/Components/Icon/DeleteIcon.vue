<script setup lang="ts">
import { XCircleIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const props = withDefaults(
    defineProps<{
        version?: 'circle' | 'mark'
    }>(),
    {
        version: 'circle',
    }
)
</script>

<template>
    <XCircleIcon
        v-if="props.version === 'circle'"
        class="h-5 w-5 cursor-pointer text-gray-400 hover:text-red-300"
    />

    <XMarkIcon
        v-else
        class="h-5 w-5 cursor-pointer text-gray-400 hover:text-red-300"
    />
</template>
