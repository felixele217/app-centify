<script setup lang="ts">
import AnnualRevenue from '@/Components/Dashboard/Payout/AnnualRevenue.vue'
import PayoutCard from '@/Components/Dashboard/Payout/PayoutCard.vue'
import PayoutDeals from '@/Components/Dashboard/Payout/PayoutDeals.vue'
import PayoutTotals from '@/Components/Dashboard/Payout/PayoutTotals.vue'
import MainLayout from '@/Layouts/MainLayout.vue'
import { Head } from '@inertiajs/vue3'

const payoutRowObjects = [
    { title: 'Total Payout', icon: null, amount: '38.163€', subText: 'Next payout: 01.08.2023' },
    { title: 'Payout Forecast', icon: null, amount: '51.763€', subText: '-3.22% vs. last quarter' },
    { title: 'Avg. Payout', icon: null, amount: '1.733€', subText: '+1.54% vs. last quarter' },
    { title: 'Statements', icon: null, amount: '5', subText: '2 / 5 need attention' },
]
</script>

<template>
    <Head title="Dashboard" />

    <MainLayout class="flex h-full flex-col">
        <div class="mb-5 flex gap-5">
            <PayoutCard
                v-for="object in payoutRowObjects"
                v-bind="object"
                class="w-1/4"
            />
        </div>

        <div class="flex grow gap-5">
            <div class="flex w-1/2 flex-col gap-5">
                <AnnualRevenue />

                <PayoutTotals />
            </div>

            <PayoutDeals class="w-1/2" />
        </div>
    </MainLayout>
</template>
