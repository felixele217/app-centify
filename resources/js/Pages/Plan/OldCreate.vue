<script setup lang="ts">
import OldUpsertPlanCard from '@/Components/Plan/OldUpsertPlanCard.vue'
import UpsertPlanCard from '@/Components/Plan/OldUpsertPlanCard.vue'
import Agent from '@/types/Agent'
import { KickerTypeEnum } from '@/types/Enum/KickerTypeEnum'
import { PlanCycleEnum } from '@/types/Enum/PlanCycleEnum'
import { SalaryTypeEnum } from '@/types/Enum/SalaryTypeEnum'
import { TargetVariableEnum } from '@/types/Enum/TargetVariableEnum'

const props = defineProps<{
    agents: Array<Pick<Agent, 'id' | 'name'>>
    target_variable_options: Array<TargetVariableEnum>
    payout_frequency_options: Array<PlanCycleEnum>
    kicker_type_options: Array<KickerTypeEnum>
    salary_type_options: Array<SalaryTypeEnum>
}>()
</script>

<template>
    <OldUpsertPlanCard
        :agents="props.agents"
        :target_variable_options="props.target_variable_options"
        :payout_frequency_options="props.payout_frequency_options"
        :kicker_type_options="props.kicker_type_options"
        :salary_type_options="props.salary_type_options"
    />
</template>
