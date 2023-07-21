<script setup lang="ts">
import PayoutCard from '@/Components/Dashboard/Payout/PayoutCard.vue'
import QuotaAttainment from '@/Components/Dashboard/Payout/QuotaAttainment.vue'
import TotalPayoutByEmployee from '@/Components/Dashboard/Payout/TotalPayoutByEmployee.vue'
import BanknotesIcon from '@/Components/Icon/BanknotesIcon.vue'
import DealIcon from '@/Components/Icon/DealIcon.vue'
import Agent from '@/types/Agent'
import euroDisplay from '@/utils/euroDisplay'
import sum from '@/utils/sum'
import { Head } from '@inertiajs/vue3'

const props = defineProps<{
    agents: Array<Agent>
    open_deal_count: number
}>()

const totalComission = euroDisplay(sum(props.agents.map((agent) => agent.commission! / 100)))

const payoutRowObjects: Array<{
    title: string
    icon: any
    amount: number | string
    subText: string
    link?: string
}> = [
    {
        title: 'Total Payout',
        icon: BanknotesIcon,
        amount: totalComission,
        subText: 'next payout: 01.08.2023',
    },
    {
        title: 'To-Dos',
        icon: DealIcon,
        amount: props.open_deal_count || 0,
        subText: 'need attention',
        link: route('deals.index') + '?scope=open',
    },
]
</script>

<template>
    <div>
        <Head title="Dashboard" />
        <div class="mb-5 grid grid-cols-4 gap-5">
            <PayoutCard
                v-for="object in payoutRowObjects"
                v-bind="object"
            />

            <QuotaAttainment
                :agents="props.agents"
                class="col-span-2"
            />
        </div>

        <TotalPayoutByEmployee :agents="props.agents" />
    </div>
</template>
