<script setup lang="ts">
import formatTime from '@/utils/Date/formatTime'

const props = defineProps<{
    lastSynced: Date | null
}>()
</script>

<template>
    <p class="text-xs text-gray-500 text-right">last synced:<br/> {{ formatTime(props.lastSynced) }}</p>
</template>
