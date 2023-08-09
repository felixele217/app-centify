<script setup lang="ts">
import Agent from '@/types/Agent'
import { UpsertPlanForm } from '@/types/Form/UpsertPlanForm'
import formatDate from '@/utils/Date/formatDate'
import euroDisplay from '@/utils/euroDisplay'

const props = defineProps<{
    form: UpsertPlanForm
    agents: Array<Agent>
}>()

function firstAssignedAgent() {
    return props.agents.filter((agent) => agent.id === props.form.assigned_agent_ids[0])[0]
}
</script>

<template>
    <div class="mb-5 pt-5 text-sm">
        <p class="text-base font-semibold">Description</p>

        <p class="mt-2">
            The plan
            <span class="font-semibold">{{ form.name || '{plan_name}' }}</span>
            will be renewed automatically for each
            <span class="font-semibold">{{ form.payout_frequency || '{interval}' }},</span>
            starting on
            <span class="font-semibold">{{
                formatDate(form.start_date) !== '-' ? formatDate(form.start_date) : '' || '{start_date}'
            }}</span>
            and is assigned to
            <span class="font-semibold">{{ form.assigned_agent_ids.length }}</span>
            agents.
        </p>

        <p class="mt-2">
            The variable
            <span class="font-semibold">{{ form.target_variable || '{target_variable}' }}</span>
            is attributed to the plan and triggered by
            <span class="font-semibold">{{ form.trigger || '{trigger}' }}.</span>

            For this variable, you set the individual monthly target at
            <span class="font-semibold">{{ form.target_amount_per_month || '{target_amount_per_month}' }}€. </span>
        </p>

    </div>
</template>
