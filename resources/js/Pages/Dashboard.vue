<script setup lang="ts">
import AnnualRevenue from '@/Components/Dashboard/Payout/AnnualRevenue.vue'
import PayoutCard from '@/Components/Dashboard/Payout/PayoutCard.vue'
import TotalPayoutByEmployee from '@/Components/Dashboard/Payout/TotalPayoutByEmployee.vue'
import BanknotesIcon from '@/Components/Icon/BanknotesIcon.vue'
import TodoIcon from '@/Components/Icon/TodoIcon.vue'
import Agent from '@/types/Agent'
import euroDisplay from '@/utils/euroDisplay'
import sum from '@/utils/sum'
import { Head } from '@inertiajs/vue3'

const props = defineProps<{
    agents: Array<Agent>
    todo_count: number
}>()

const totalComission = euroDisplay(sum(props.agents.map((agent) => agent.commission!)))

const payoutRowObjects = [
    {
        title: 'Total Payout',
        icon: BanknotesIcon,
        amount: totalComission,
        subText: 'Next payout: 01.08.2023',
    },
    {
        title: 'To-Dos',
        icon: TodoIcon,
        amount: props.todo_count,
        subText: 'need attention',
        link: route('todos.index'),
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

            <AnnualRevenue
                :agents="props.agents"
                class="col-span-2"
            />
        </div>

        <TotalPayoutByEmployee :agents="props.agents" />
    </div>
</template>
