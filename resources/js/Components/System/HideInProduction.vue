<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
</script>

<template>
    <div class="flex items-center gap-1">
        <p class="text-red-500">!</p>
        <slot v-if="usePage().props.environment !== 'production'" />
        <p class="text-red-500">!</p>
    </div>
</template>
