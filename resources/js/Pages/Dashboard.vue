<script setup lang="ts">
import AnnualRevenue from '@/Components/Dashboard/Payout/AnnualRevenue.vue'
import PayoutCard from '@/Components/Dashboard/Payout/PayoutCard.vue'
import TotalPayoutByEmployee from '@/Components/Dashboard/Payout/TotalPayoutByEmployee.vue'
import BanknotesIcon from '@/Components/Icon/BanknotesIcon.vue'
import TodoIcon from '@/Components/Icon/TodoIcon.vue'
import Agent from '@/types/Agent'
import euroDisplay from '@/utils/euroDisplay'
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'
const props = defineProps<{
    agents: Array<Agent>
}>()

const totalPayout = computed(() => props.agents.map(agent => agent.commission!).reduce((accumulator, current) => accumulator + current, 0))

const payoutRowObjects = [
    { title: 'Total Payout', icon: BanknotesIcon, amount: euroDisplay(totalPayout.value), subText: 'Next payout: 01.08.2023' },
    { title: 'To-Dos', icon: TodoIcon, amount: '5', subText: 'need attention' },
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
            <AnnualRevenue class="col-span-2" />
        </div>

        <TotalPayoutByEmployee :agents="props.agents"/>
    </div>
</template>
