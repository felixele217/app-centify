<script setup lang="ts">
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
            The
            <span class="font-semibold">{{ props.form.plan_cycle || '{interval}' }}</span>
            plan
            <span class="font-semibold">{{ props.form.name || '{plan_name}' }}</span>
            is assigned to
            <span class="font-semibold">{{ props.form.assigned_agents.length }}</span>
            agent(s) and starts on
            <span class="font-semibold">{{
                formatDate(form.start_date) !== '-' ? formatDate(props.form.start_date) : '' || '{start_date}'
            }}</span
            >.
        </p>

        <p class="mt-2">
            Your agents are incentivised on
            <span class="font-semibold">{{ props.form.target_variable || '{target_variable}' }}</span>
            as the target variable. The individual monthly target is set to
            <span class="font-semibold">{{
                euroDisplay(props.form.target_amount_per_month, 0) || '{target_amount}'
            }}</span>
            yielding a 100% quota attainment.
        </p>

        <p class="mt-2">
            A change of
            <span class="font-semibold">{{ props.form.trigger || '{trigger}' }}</span>
            within your CRM system triggers the target variable into the plan.
        </p>

        <p
            class="mt-2"
            v-if="props.form.cap"
        >
            The target variable <span class="font-semibold">is capped</span> to an amount of
            <span class="font-semibold">{{ euroDisplay(props.form.cap, 0) || '{cap_value}' }}</span>
            per
            <span class="font-semibold">{{ props.form.target_variable || '{target_variable}' }}</span
            >.
        </p>

        <p
            class="mt-2"
            v-if="props.form.cliff.threshold_in_percent"
        >
            An agent must
            <span class="font-semibold"
                >achieve at least {{ props.form.cliff.threshold_in_percent || '{cliff_value}' }}
            </span>
            % of the quota
            <span class="font-semibold"> to qualify for a commission. </span>
        </p>

        <p
            class="mt-2"
            v-if="
                props.form.kicker.threshold_in_percent ||
                props.form.kicker.payout_in_percent ||
                props.form.kicker.salary_type
            "
        >
            An agent must
            <span class="font-semibold"
                >achieve at least {{ props.form.kicker.threshold_in_percent || '{kicker_treshhold}' }}</span
            >% of the quota to earn a kicker equal to
            <span class="font-semibold">{{ props.form.kicker.payout_in_percent || '{kicker_payout}' }}</span
            >% of <span class="font-semibold">{{ props.form.kicker.salary_type || '{salary_type}' }}</span
            >.
        </p>
    </div>
</template>
