<script setup lang="ts">
import initials from '@/utils/initials'

const props = defineProps<{
    name: string
}>()
</script>

<template>
    <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-gray-200">
        <p class="text-sm text-gray-800">{{ initials(props.name) }}</p>
    </div>
</template>
