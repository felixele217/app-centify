<script setup lang="ts">
import MainLayout from '@/Layouts/MainLayout.vue';
import type User from '@/types/User'

const props = defineProps<{
    agents: Array<User>
}>()
</script>

<template>
    <MainLayout />
</template>
