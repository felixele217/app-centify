<script setup lang="ts">
import type Plan from '@/types/Plan'
import euroDisplay from '@/utils/euroDisplay'
import formatDate from '@/utils/formatDate'
import notify from '@/utils/notify'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import TertiaryButton from '../Buttons/TertiaryButton.vue'
import Card from '../Card.vue'
import BanknotesIcon from '../Icon/BanknotesIcon.vue'
import CalendarIcon from '../Icon/CalendarIcon.vue'
import RecurIcon from '../Icon/RecurIcon.vue'
import TeamIcon from '../Icon/TeamIcon.vue'
import Modal from '../Modal.vue'

const props = defineProps<{
    plan: Plan
}>()

function deletePlan(planId: number): void {
    router.delete(route('plans.destroy', planId), {
        onSuccess: () => {
            notify('Plan deleted', 'This plan was removed from all of your agents.')
            planIdBeingDeleted.value = null
        },
    })
}

const planIdBeingDeleted = ref<number | null>()
</script>

<template>
    <Card class="group flex justify-between hover:bg-gray-50">
        <div>
            <h2>{{ props.plan.name }}</h2>

            <div class="mt-4 flex items-center gap-1">
                <CalendarIcon class="text-gray-400" />
                <p class="-mb-0.5 text-sm text-gray-600">
                    starts
                    <span class="font-semibold text-gray-900">
                        {{ formatDate(props.plan.start_date) }}
                    </span>
                </p>
            </div>

            <div class="mt-2 flex items-center gap-1">
                <RecurIcon class="text-gray-400" />
                <p class="-mb-0.5 text-sm text-gray-600">
                    occurs
                    <span class="font-semibold text-gray-900">
                        {{ props.plan.payout_frequency }}
                    </span>
                </p>
            </div>

            <div class="mt-2 flex items-center gap-1">
                <BanknotesIcon class="text-gray-400" />
                <p class="-mb-0.5 text-sm text-gray-600">
                    targets
                    <span class="font-semibold text-gray-900">
                        {{ props.plan.target_variable }}
                    </span>
                </p>
            </div>

            <h3 class="mt-6">{{ euroDisplay(props.plan.target_amount_per_month) }}</h3>
            <p class="text-sm text-gray-600">monthly target</p>
        </div>

        <div class="flex flex-col items-end justify-between">
            <div class="flex flex-col items-end gap-2">
                <div class="flex items-center gap-1 text-gray-600">
                    <p>{{ props.plan.agents_count }}</p>
                    <TeamIcon class="text-gray-600" />
                </div>
                <TertiaryButton
                    @click="planIdBeingDeleted = props.plan.id"
                    text="Delete"
                    class="invisible group-hover:visible"
                />
            </div>

            <p>Created by {{ props.plan.creator.name }}</p>
        </div>

        <Modal
            @modal-action="deletePlan(planIdBeingDeleted!)"
            :is-negative-action="true"
            :isOpen="!!planIdBeingDeleted"
            @close-modal="planIdBeingDeleted = null"
            button-text="Delete"
            title="Delete Plan"
            :description="'Are you sure you want to delete this Plan? \nIt will be removed from all of your agents and its metrics won\'t be accessible anymore.'"
        />
    </Card>
</template>
