<script setup lang="ts">
import Checkbox from '@/Components/Form/Checkbox.vue'
import CurrencyInput from '@/Components/Form/CurrencyInput.vue'
import DateInput from '@/Components/Form/DateInput.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import SelectWithDescription from '@/Components/Form/SelectWithDescription.vue'
import Tooltip from '@/Components/Tooltip.vue'
import { AgentStatusEnum } from '@/types/Enum/AgentStatusEnum'
import { ContinuationOfPayTimeScopeEnum } from '@/types/Enum/ContinuationOfPayTimeScopeEnum'
import { continuationOfPayTimeScopeToDescription } from '@/utils/Descriptions/continuationOfPayTimeScopeToDescription'
import enumOptionsToSelectOptionWithDescription from '@/utils/Descriptions/enumOptionsToSelectOptionWithDescription'
import { InertiaForm, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps<{
    form: InertiaForm<{
        status: AgentStatusEnum
        paid_leave: {
            start_date: Date | null
            end_date: Date | null
            continuation_of_pay_time_scope: string
            sum_of_commissions: number
        }
    }>
}>()

const continuationOfPayTimeScopeOptions = usePage().props
    .continuation_of_pay_time_scope_options as Array<ContinuationOfPayTimeScopeEnum>

const employed28OrMoreDays = ref<boolean>(true)
</script>

<template>
    <div
        class="flex"
        v-if="props.form.status === 'sick'"
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
                :date="props.form.paid_leave.start_date"
                @date-changed="(newDate: Date) => (props.form.paid_leave.start_date = newDate)"
            />

            <InputError
                class="mt-2"
                :message="(props.form.errors as Record<string, string>)['paid_leave.start_date']"
            />
        </div>

        <div>
            <InputLabel
                for="end_date"
                value="End Date"
                :required="props.form.status === 'on vacation'"
            />

            <DateInput
                :date="props.form.paid_leave.end_date"
                @date-changed="(newDate: Date) => (props.form.paid_leave.end_date = newDate)"
            />

            <InputError
                class="mt-2"
                :message="(props.form.errors as Record<string, string>)['paid_leave.end_date']"
            />
        </div>

        <div>
            <InputLabel
                value="Continuation of pay based on.."
                required
            />

            <SelectWithDescription
                :options="
                    enumOptionsToSelectOptionWithDescription(
                        continuationOfPayTimeScopeOptions,
                        continuationOfPayTimeScopeToDescription
                    )
                "
                @option-selected="(optionTitle: string) => props.form.paid_leave.continuation_of_pay_time_scope = optionTitle"
            />

            <InputError
                class="mt-2"
                :message="(props.form.errors as Record<string, string>)['paid_leave.continuation_of_pay_time_scope']"
            />
        </div>

        <div>
            <InputLabel
                value="Sum of commissions earned during this time.."
                required
            />

            <CurrencyInput
                :value="props.form.paid_leave.sum_of_commissions"
                @set-value="(value: number) => (props.form.paid_leave.sum_of_commissions = value)"
            />

            <InputError
                class="mt-2"
                :message="(props.form.errors as Record<string, string>)['paid_leave.sum_of_commissions']"
            />
        </div>
    </div>
</template>
