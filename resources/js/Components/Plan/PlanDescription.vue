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
            <span class="font-semibold">{{ formatDate(form.start_date) || '{start_date}' }}</span>
            and is assigned to
            <span class="font-semibold">{{ form.assigned_agent_ids.length }}</span>
            agents.
        </p>

        <p class="mt-2">
            The variable
            <span class="font-semibold">{{ form.target_variable || '{target_variable}' }}</span>
            is attributed to the plan and triggered by
            <span class="font-semibold">{{ form.trigger || '{trigger}' }}</span>

            For this variable, you set the individual monthly target at
            <span class="font-semibold">{{ form.target_amount_per_month || '{target_amount_per_month}' }}. </span>
        </p>

        <p class="mt-5 text-base font-semibold">Example</p>

        <p class="mt-2">
            If
            <span class="font-semibold">{{
                props.agents.filter((agent) => agent.id === form.assigned_agent_ids[0])[0]?.name || '{assigned_agent}'
            }}</span>
            of the plan achieves
            <span class="font-semibold">
                {{ form.target_amount_per_month || '{target_amount_per_month}' }}
                {{ form.payout_frequency === 'quarterly' ? '*3' : '' }}
            </span>
            of
            <span class="font-semibold">{{ form.target_variable || '{target_variable}' }}</span>
            within a
            <span class="font-semibold">{{ form.payout_frequency || '{payout_frequency}' }},</span>
            the commission equals to
            <br />
        </p>
        <p class="mt-2">
            <span class="font-semibold">
                ({{
                    `${euroDisplay(firstAssignedAgent()?.on_target_earning) || ''} (${
                        firstAssignedAgent()?.name || ''
                    }On Target Earning)` || "(Agent's On Target Earning)"
                }}
                -
                {{
                    `${euroDisplay(firstAssignedAgent()?.base_salary) || ''} (${
                        firstAssignedAgent()?.name || ''
                    }Base Salary)` || "(Agent's Base Salary)"
                }}
                / 12 (months))€.
            </span>
        </p>
    </div>
</template>
