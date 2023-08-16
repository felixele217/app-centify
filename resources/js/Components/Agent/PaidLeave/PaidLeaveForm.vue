<script setup lang="ts">
import Checkbox from '@/Components/Form/Checkbox.vue'
import CurrencyInput from '@/Components/Form/CurrencyInput.vue'
import DateInput from '@/Components/Form/DateInput.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import SelectWithDescription from '@/Components/Form/SelectWithDescription.vue'
import InfoIcon from '@/Components/Icon/InfoIcon.vue'
import RadioCards from '@/Components/RadioCards.vue'
import Tooltip from '@/Components/Tooltip.vue'
import Agent from '@/types/Agent'
import { AgentStatusEnum } from '@/types/Enum/AgentStatusEnum'
import { ContinuationOfPayTimeScopeEnum } from '@/types/Enum/ContinuationOfPayTimeScopeEnum'
import { continuationOfPayTimeScopeToDescription } from '@/utils/Descriptions/continuationOfPayTimeScopeToDescription'
import enumOptionsToSelectOptionWithDescription from '@/utils/Descriptions/enumOptionsToSelectOptionWithDescription'
import markedRangesFromRangeObjects from '@/utils/markedRangesFromRangeObjects'
import { InertiaForm, usePage } from '@inertiajs/vue3'
import { watch } from 'vue'
import PaidLeaveCard from './PaidLeaveCard.vue'

const props = defineProps<{
    form: InertiaForm<{
        reason: AgentStatusEnum
        start_date: Date | null
        end_date: Date | null
        continuation_of_pay_time_scope: ContinuationOfPayTimeScopeEnum | ''
        sum_of_commissions: number | null
        employed_28_or_more_days: boolean
    }>
    agentId?: number
}>()

defineEmits<{
    deleted: []
}>()

watch(
    () => props.form.reason,
    async (reason: AgentStatusEnum) => {
        if (reason === 'on vacation') {
            props.form.continuation_of_pay_time_scope = 'last quarter'
        } else {
            props.form.continuation_of_pay_time_scope = ''
        }
    }
)

const continuationOfPayTimeScopeOptions = usePage().props
    .continuation_of_pay_time_scope_options as Array<ContinuationOfPayTimeScopeEnum>

const agent = (usePage().props.agents as Array<Agent>).filter((agent) => agent.id === props.agentId)[0]

function agentPaidLeaveRanges() {
    if (!agent) {
        return undefined
    }

    return [
        ...markedRangesFromRangeObjects(
            agent!.paid_leaves.filter((paid_leave) => paid_leave.reason === 'sick'),
            'purple'
        ),
        ...markedRangesFromRangeObjects(
            agent!.paid_leaves.filter((paid_leave) => paid_leave.reason === 'on vacation'),
            'yellow'
        ),
    ]
}
</script>

<template>
    <RadioCards
        @radio-clicked="(optionTitle: string) => props.form.reason = (optionTitle as AgentStatusEnum)"
        :options="[
            {
                title: 'sick',
                backgroundColor: 'bg-purple-100',
                ringColor: 'ring-purple-700',
            },
            {
                title: 'on vacation',
                backgroundColor: 'bg-yellow-100',
                ringColor: 'ring-yellow-700',
            },
        ]"
        :default="props.form.reason"
    />

    <div class="space-y-4">
        <div>
            <InputLabel
                for="start_date"
                value="Start Date"
                required
            />

            <DateInput
                :current-date="props.form.start_date"
                @date-changed="(newDate: Date) => (props.form.start_date = newDate)"
                :marked-ranges="agentPaidLeaveRanges()"
            />

            <InputError
                class="mt-2"
                :message="(props.form.errors as Record<string, string>)['start_date']"
            />
        </div>

        <div>
            <div class="flex items-center gap-1">
                <InputLabel
                    for="end_date"
                    value="End Date"
                    :required="props.form.reason === 'on vacation'"
                />

                <InfoIcon
                    hover-text="Please add the end date of the sickness. If not done immediately, this will pop up as a task in your to-dos.
                    Only if the end date is entered a proper calculation can run."
                    class="whitespace-pre-line"
                    v-if="props.form.reason === 'sick'"
                />
            </div>

            <DateInput
                :current-date="props.form.end_date"
                @date-changed="(newDate: Date) => (props.form.end_date = newDate)"
                :marked-ranges="agentPaidLeaveRanges()"
            />

            <InputError
                class="mt-2"
                :message="(props.form.errors as Record<string, string>)['end_date']"
            />
        </div>

        <div>
            <div class="flex items-center gap-1">
                <InputLabel
                    value="Continuation of pay based on.."
                    :required="props.form.reason === 'on vacation'"
                />

                <InfoIcon
                    hover-text="The law prescribes the earned commission of the last quarter as basis."
                    v-if="props.form.reason === 'on vacation'"
                />

                <InfoIcon
                    hover-text="You have to select a basis for the calculation as the law does not directly prescribe what to do."
                    v-else
                />
            </div>

            <SelectWithDescription
                :options="
                    enumOptionsToSelectOptionWithDescription(
                        continuationOfPayTimeScopeOptions,
                        continuationOfPayTimeScopeToDescription
                    )
                "
                :disabled="props.form.reason === 'on vacation'"
                v-model="props.form.continuation_of_pay_time_scope"
            />

            <InputError
                class="mt-2"
                :message="(props.form.errors as Record<string, string>)['continuation_of_pay_time_scope']"
            />
        </div>

        <div>
            <div class="flex items-center gap-1">
                <InputLabel
                    value="Sum of commissions earned during this time.."
                    required
                />

                <InfoIcon
                    hover-text="If the field is empty, please look up the number from your payroll and insert it.
                    An empty field means that you are new to Centify and we don't have enough data yet."
                    class="whitespace-pre-line"
                />
            </div>

            <CurrencyInput v-model="props.form.sum_of_commissions" />

            <InputError
                class="mt-2"
                :message="(props.form.errors as Record<string, string>)['sum_of_commissions']"
            />
        </div>

        <div
            class="flex pt-1"
            v-if="props.form.reason === 'sick'"
        >
            <Tooltip
                text="Info: If the employee hasn’t been with the company for at least 28 days, he or she is not eligible for any
                payment by the company (continuation of pay). Instead, he or she has to contact the health insurance provider for payment"
            >
                <Checkbox
                    label="Employee has been employed for more than 28 calendar days"
                    name="employed_long_enough"
                    v-model:checked="props.form.employed_28_or_more_days"
                />

                <InputError
                    class="mt-2"
                    :message="props.form.errors.employed_28_or_more_days"
                />
            </Tooltip>
        </div>

        <div>
            <div
                class="mb-1 mt-7 flex items-center gap-2"
                v-if="agent.paid_leaves.length"
            >
                <h4>Recent Paid Leaves</h4>
            </div>

            <PaidLeaveCard
                v-for="paidLeave of agent.paid_leaves"
                :paid-leave="paidLeave"
                :key="paidLeave.id"
                @deleted="$emit('deleted')"
            />
        </div>
    </div>
</template>
