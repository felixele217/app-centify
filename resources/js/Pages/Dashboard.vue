<script setup lang="ts">
import PayoutRow from '@/Components/Dashboard/PayoutRow.vue'
import MainLayout from '@/Layouts/MainLayout.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
    <Head title="Dashboard" />

    <MainLayout>
        <h1 class="mb-4">Dashboard</h1>

        <PayoutRow />
    </MainLayout>
</template>
