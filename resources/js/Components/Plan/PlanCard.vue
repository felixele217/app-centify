<script setup lang="ts">
import type Plan from '@/types/Plan'
import euroDisplay from '@/utils/euroDisplay'
import Card from '../Card.vue'
import BanknotesIcon from '../Icon/BanknotesIcon.vue'
import CalendarIcon from '../Icon/CalendarIcon.vue'
import RecurIcon from '../Icon/RecurIcon.vue'
import TeamIcon from '../Icon/TeamIcon.vue'

const props = defineProps<{
    plan: Plan
}>()
</script>

<template>
    <Card>
        <div class="flex justify-between">
            <h2>{{ props.plan.name }}</h2>
            <div class="flex items-center gap-1 text-gray-600">
                <p>{{ props.plan.agents_count }}</p>
                <TeamIcon class="text-gray-600" />
            </div>
        </div>

        <div class="mt-4 flex items-center gap-1">
            <CalendarIcon />
            <p class="-mb-0.5 text-sm text-gray-600">
                starts
                <span class="font-semibold text-gray-900">
                    {{ props.plan.start_date }}
                </span>
            </p>
        </div>

        <div class="mt-2 flex items-center gap-1">
            <RecurIcon />
            <p class="-mb-0.5 text-sm text-gray-600">
                occurs
                <span class="font-semibold text-gray-900">
                    {{ props.plan.payout_frequency }}
                </span>
            </p>
        </div>

        <div class="mt-2 flex items-center gap-1">
            <BanknotesIcon />
            <p class="-mb-0.5 text-sm text-gray-600">
                targets
                <span class="font-semibold text-gray-900">
                    {{ props.plan.target_variable }}
                </span>
            </p>
        </div>

        <h3 class="mt-6">{{ euroDisplay(props.plan.target_amount_per_month) }}</h3>

        <div class="flex justify-between text-sm text-gray-600">
            <p>monthly target</p>
            <p>Created by {{ props.plan.creator.name }}</p>
        </div>
    </Card>
</template>
