<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
</script>

<template>
    <slot v-if="usePage().props.environment !== 'production'" />
</template>
