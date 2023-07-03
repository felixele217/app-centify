<script setup lang="ts">
import type Plan from '@/types/Plan'
import euroDisplay from '@/utils/euroDisplay'
import Card from '../Card.vue'
import TeamIcon from '../Icon/TeamIcon.vue'

const props = defineProps<{
    plan: Plan
}>()
</script>

<template>
    <Card>
        <div class="flex items-center justify-between">
            <h3>{{ props.plan.name }}</h3>
            <div class="flex items-center gap-1 text-gray-600">
                <p>{{ props.plan.agents_count }}</p>
                <TeamIcon class="text-gray-600" />
            </div>
        </div>
        <h2 class="mt-6">{{ euroDisplay(props.plan.target_amount_per_month * 12) }}</h2>
        <div class="flex justify-between text-sm text-gray-500">
            <p>Commission</p>
            <p>Created by {{ props.plan.creator.name }}</p>
        </div>
    </Card>
</template>
