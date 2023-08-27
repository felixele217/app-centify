<script setup lang="ts">
import PaidLeaveSlideOver from '@/Components/Agent/PaidLeave/PaidLeaveSlideOver.vue'
import QuotaAttainment from '@/Components/Dashboard/Payout/QuotaAttainment.vue'
import Todos from '@/Components/Dashboard/Payout/Todos.vue'
import TotalCommission from '@/Components/Dashboard/Payout/TotalCommission.vue'
import TotalCommissionByEmployee from '@/Components/Dashboard/Payout/TotalCommissionByEmployee.vue'
import Agent from '@/types/Agent'
import { AgentStatusEnum } from '@/types/Enum/AgentStatusEnum'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps<{
    agents: Array<Agent>
    open_deal_count: number
}>()

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
            <TotalCommission :agents="props.agents" />

            <Todos :open-deal-count="props.open_deal_count" />

            <QuotaAttainment
                :agents="props.agents"
                class="col-span-2"
            />
        </div>

        <TotalCommissionByEmployee
            :agents="props.agents"
            @open-paid-leave-slide-over="handleOpenPaidLeaveSlideOver"
        />

        <PaidLeaveSlideOver
            dusk="manage-paid-leaves-slide-over"
            :is-open="!!agentIdBeingManaged"
            :agentId="agentIdBeingManaged"
            :reason="paidLeaveReason"
            @close-slide-over="agentIdBeingManaged = undefined"
        />
    </div>
</template>
