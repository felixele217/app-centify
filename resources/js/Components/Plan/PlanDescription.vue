<script setup lang="ts">
import Agent from '@/types/Agent'
import { UpsertPlanForm } from '@/types/Form/UpsertPlanForm'
import formatDate from '@/utils/Date/formatDate'
import euroDisplay from '@/utils/euroDisplay'

const props = defineProps<{
    form: UpsertPlanForm
}>()
</script>

<template>
    <div class="mb-5 pt-5 text-sm">
        <p class="text-base font-semibold">Description</p>

        <p class="mt-2">
            The plan
            <span class="font-semibold">{{ props.form.name || '{plan_name}' }}</span>
            will be renewed
            <span class="font-semibold">{{ props.form.plan_cycle || '{interval}' }},</span>
            starting on
            <span class="font-semibold">{{
                formatDate(form.start_date) !== '-' ? formatDate(props.form.start_date) : '' || '{start_date}'
            }}</span>
            and is assigned to
            <span class="font-semibold">{{ props.form.assigned_agent_ids.length }}</span>
            agent(s).
        </p>

        <p class="mt-2">
            The variable
            <span class="font-semibold">{{ props.form.target_variable || '{target_variable}' }}</span>
            is attributed to the plan and triggered by
            <span class="font-semibold">{{ props.form.trigger || '{trigger}' }}.</span>

            For this variable, you set the individual monthly target at
            <span class="font-semibold"
                >{{ euroDisplay(props.form.target_amount_per_month, 0) || '{target_amount_per_month}' }}.
            </span>
        </p>
    </div>
</template>
