<script setup lang="ts">
import PaidLeaveSlideOver from '@/Components/Agent/PaidLeave/PaidLeaveSlideOver.vue'
import PayoutCard from '@/Components/Dashboard/Payout/PayoutCard.vue'
import QuotaAttainment from '@/Components/Dashboard/Payout/QuotaAttainment.vue'
import TotalPayoutByEmployee from '@/Components/Dashboard/Payout/TotalPayoutByEmployee.vue'
import BanknotesIcon from '@/Components/Icon/BanknotesIcon.vue'
import DealIcon from '@/Components/Icon/DealIcon.vue'
import Agent from '@/types/Agent'
import { AgentStatusEnum } from '@/types/Enum/AgentStatusEnum'
import euroDisplay from '@/utils/euroDisplay'
import sum from '@/utils/sum'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps<{
    agents: Array<Agent>
    open_deal_count: number
}>()

const totalComission = euroDisplay(sum(props.agents.map((agent) => agent.commission!)))

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

const agentIdBeingManaged = ref<number>()
const paidLeaveReason = ref<AgentStatusEnum>()

function handleOpenPaidLeaveSlideOver(agentId: number, reason: AgentStatusEnum) {
    agentIdBeingManaged.value = agentId
    paidLeaveReason.value = reason
}
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

        <TotalPayoutByEmployee
            :agents="props.agents"
            @open-paid-leave-slide-over="handleOpenPaidLeaveSlideOver"
        />

        <PaidLeaveSlideOver
            :is-open="!!agentIdBeingManaged"
            :agentId="agentIdBeingManaged"
            :reason="paidLeaveReason"
            @close-slide-over="agentIdBeingManaged = undefined"
        />
    </div>
</template>
