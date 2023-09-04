<script setup lang="ts">
import type Plan from '@/types/Plan/Plan'
import formatDate from '@/utils/Date/formatDate'
import euroDisplay from '@/utils/euroDisplay'
import notify from '@/utils/notify'
import { BoltIcon } from '@heroicons/vue/24/outline'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Card from '../Card.vue'
import EditAndDeleteOptions from '../Dropdown/EditAndDeleteOptions.vue'
import BanknotesIcon from '../Icon/BanknotesIcon.vue'
import CalendarIcon from '../Icon/CalendarIcon.vue'
import RecurIcon from '../Icon/RecurIcon.vue'
import TeamIcon from '../Icon/TeamIcon.vue'
import Modal from '../Modal.vue'
import Tooltip from '../Tooltip.vue'
import PlanCardAdditionalFieldIcon from './PlanCardAdditionalFieldIcon.vue'
import roundFloat from '@/utils/roundFloat'

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
    <Card>
        <div class="flex items-center justify-between">
            <h2 class="whitespace-nowrap">{{ props.plan.name }}</h2>

            <div class="flex items-center gap-1 text-gray-600">
                <p>{{ props.plan.agents_count }}</p>
                <TeamIcon class="text-gray-600" />
                <EditAndDeleteOptions
                    @edit-action="() => router.get(route('plans.update', props.plan.id))"
                    @delete-action="() => (planIdBeingDeleted = props.plan.id)"
                />
            </div>
        </div>

        <div class="mt-4 flex h-28 justify-between gap-10">
            <div class="flex flex-col justify-evenly">
                <div class="flex items-center gap-1">
                    <RecurIcon class="text-gray-400" />
                    <p class="-mb-0.5 text-sm text-gray-600">
                        occurs
                        <span class="font-semibold text-gray-900">
                            {{ props.plan.plan_cycle }}
                        </span>
                    </p>
                </div>
                <div class="flex items-center gap-1">
                    <CalendarIcon class="text-gray-400" />
                    <p class="-mb-0.5 text-sm text-gray-600">
                        starts
                        <span class="font-semibold text-gray-900">
                            {{ formatDate(props.plan.start_date) }}
                        </span>
                    </p>
                </div>
                <div class="flex items-center gap-1">
                    <BanknotesIcon class="text-gray-400" />
                    <p class="-mb-0.5 text-sm text-gray-600">
                        targets
                        <span class="font-semibold text-gray-900">
                            {{ props.plan.target_variable }}
                        </span>
                    </p>
                </div>
                <div class="flex items-center gap-1">
                    <BoltIcon class="h-5 w-5 text-gray-400" />
                    <p class="-mb-0.5 text-sm text-gray-600">
                        triggered by
                        <span class="font-semibold text-gray-900">
                            {{ props.plan.trigger }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="flex flex-col justify-evenly">
                <Tooltip
                    placement="top"
                    :text="props.plan.kicker ? `As soon as participating agents achieve ${props.plan.kicker!.threshold_in_percent * 100}% of their quota target, they earn ${props.plan.kicker!.payout_in_percent * 100}% of their ${props.plan.kicker!.salary_type} as one-time payment.` : ''"
                >
                    <div class="flex items-center justify-end gap-1">
                        <p class="-mb-0.5 text-sm text-gray-600">Kicker</p>
                        <PlanCardAdditionalFieldIcon :exists="!!props.plan.kicker" />
                    </div>
                </Tooltip>
                <Tooltip
                    placement="top"
                    :text="props.plan.cliff ? `If participating agents don't achieve ${roundFloat(props.plan.cliff!.threshold_in_percent * 100, 0)}% of their quota target, they earn no commission.` : ''"
                >
                    <div class="flex items-center justify-end gap-1">
                        <p class="-mb-0.5 text-sm text-gray-600">Cliff</p>
                        <PlanCardAdditionalFieldIcon :exists="!!props.plan.cliff" />
                    </div>
                </Tooltip>
                <Tooltip
                    placement="top"
                    :text="props.plan.cap ? `Individual deals are included for calculations with a maximum of ${euroDisplay(props.plan.cap!.value)}.` : ''"
                >
                    <div class="flex items-center justify-end gap-1">
                        <p class="-mb-0.5 text-sm text-gray-600">Deal Cap</p>
                        <PlanCardAdditionalFieldIcon :exists="!!props.plan.cap" />
                    </div>
                </Tooltip>
            </div>
        </div>

        <div class="flex items-end justify-between">
            <div>
                <h3 class="mt-6">{{ euroDisplay(props.plan.target_amount_per_month) }}</h3>
                <p class="text-sm text-gray-600">monthly target</p>
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
