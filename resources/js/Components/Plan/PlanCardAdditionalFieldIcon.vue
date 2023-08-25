<script setup lang="ts">
import { CheckIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
    exists: boolean
}>()
</script>

<template>
    <CheckIcon
        v-if="props.exists"
        class="h-6 w-6 text-green-700"
    />
    <XMarkIcon
        v-else
        class="h-6 w-6 text-red-700"
    />
</template>
