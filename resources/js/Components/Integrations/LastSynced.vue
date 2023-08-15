<script setup lang="ts">
import formatTime from '@/utils/Date/formatTime'

const props = defineProps<{
    lastSynced?: Date | null
}>()
console.log(props.lastSynced)
</script>

<template>
    <p class="text-right text-xs text-gray-500">
        last synced:<br />
        {{ props.lastSynced ? formatTime(props.lastSynced) : 'n/a' }}
    </p>
</template>
