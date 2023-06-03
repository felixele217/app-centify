<script setup lang="ts">
import PayoutAmounts from '@/Components/Dashboard/Payout/PayoutAmounts.vue'
import PayoutDates from '@/Components/Dashboard/Payout/PayoutDates.vue'
import PayoutDeals from '@/Components/Dashboard/Payout/PayoutDeals.vue'
import PayoutTotals from '@/Components/Dashboard/Payout/PayoutTotals.vue'
import Revenue from '@/Components/Dashboard/Payout/Revenue.vue'
import MainLayout from '@/Layouts/MainLayout.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
    <Head title="Dashboard" />

    <MainLayout>
        <h1 class="mb-6">Dashboard</h1>

        <div class="mb-12 flex gap-10">
            <PayoutDates class="w-1/3" />

            <PayoutAmounts class="w-1/3" />

            <Revenue class="w-1/3" />
        </div>

        <div class="flex gap-10">
            <PayoutTotals class="w-1/2" />

            <PayoutDeals class="w-1/2" />
        </div>
    </MainLayout>
</template>
