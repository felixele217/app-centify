<script setup lang="ts">
import Checkbox from '@/Components/Form/Checkbox.vue'
import CurrencyInput from '@/Components/Form/CurrencyInput.vue'
import DateInput from '@/Components/Form/DateInput.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import SelectWithDescription from '@/Components/Form/SelectWithDescription.vue'
import InfoIcon from '@/Components/Icon/InfoIcon.vue'
import Tooltip from '@/Components/Tooltip.vue'
import Agent from '@/types/Agent'
import { AgentStatusEnum } from '@/types/Enum/AgentStatusEnum'
import { ContinuationOfPayTimeScopeEnum } from '@/types/Enum/ContinuationOfPayTimeScopeEnum'
import { continuationOfPayTimeScopeToDescription } from '@/utils/Descriptions/continuationOfPayTimeScopeToDescription'
import enumOptionsToSelectOptionWithDescription from '@/utils/Descriptions/enumOptionsToSelectOptionWithDescription'
import markedRangesFromRangeObjects from '@/utils/markedRangesFromRangeObjects'
import { InertiaForm, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'
import PaidLeaveCard from './PaidLeaveCard.vue'

const props = defineProps<{
    form: InertiaForm<{
        reason: AgentStatusEnum
        start_date: Date | null
        end_date: Date | null
        continuation_of_pay_time_scope: string
        sum_of_commissions: number | null
    }>
    agentId?: number
}>()

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
            'green'
        ),
        ...markedRangesFromRangeObjects(
            agent!.paid_leaves.filter((paid_leave) => paid_leave.reason === 'on vacation'),
            'yellow'
        ),
    ]
}

const employed28OrMoreDays = ref<boolean>(true)
</script>

<template>
    <div
        class="flex"
        v-if="props.form.reason === 'sick'"
    >
        <Tooltip
            text="Info: If the employee hasn’t been with the company for at least 28 days, he or she is not eligible for any
        payment by the company (continuation of pay). Instead, he or she has to contact the health insurance provider for payment"
        >
            <Checkbox
                label="Employee has been employed for more than 28 calendar days"
                name="employed_long_enough"
                v-model:checked="employed28OrMoreDays"
            />
        </Tooltip>
    </div>

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
            <InputLabel
                for="end_date"
                value="End Date"
                :required="props.form.reason === 'on vacation'"
            />

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
            <div class="flex gap-1">
                <InputLabel
                    value="Continuation of pay based on.."
                    required
                />

                <InfoIcon
                    hover-text="Pay is based on the last quarter as per German law."
                    v-if="props.form.reason === 'on vacation'"
                />
            </div>

            <SelectWithDescription
                :options="
                    enumOptionsToSelectOptionWithDescription(
                        continuationOfPayTimeScopeOptions,
                        continuationOfPayTimeScopeToDescription
                    )
                "
                @option-selected="(optionTitle: string) => props.form.continuation_of_pay_time_scope = optionTitle"
                :default-title="props.form.reason === 'on vacation' ? 'last quarter' : undefined"
                :disabled="props.form.reason === 'on vacation'"
            />

            <InputError
                class="mt-2"
                :message="(props.form.errors as Record<string, string>)['continuation_of_pay_time_scope']"
            />
        </div>

        <div>
            <InputLabel
                value="Sum of commissions earned during this time.."
                required
            />

            <CurrencyInput
            v-model="props.form.sum_of_commissions"
            />

            <InputError
                class="mt-2"
                :message="(props.form.errors as Record<string, string>)['sum_of_commissions']"
            />
        </div>

        <div>
            <h4 class="mt-20">Recent Paid Leaves</h4>

            <PaidLeaveCard
                class="mt-2"
                v-for="paidLeave of agent.paid_leaves"
                :paid-leave="paidLeave"
            />
        </div>
    </div>
</template>
