<script setup lang="ts">
import Card from '@/Components/Card.vue'
import CurrencyInput from '@/Components/Form/CurrencyInput.vue'
import DateInput from '@/Components/Form/DateInput.vue'
import FormButtons from '@/Components/Form/FormButtons.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import MultiSelect from '@/Components/Form/MultiSelect.vue'
import SelectWithDescription from '@/Components/Form/SelectWithDescription.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import AdditionalField from '@/Components/Plan/AdditionalField.vue'
import Agent from '@/types/Agent'
import { PayoutFrequencyEnum } from '@/types/Enum/PayoutFrequencyEnum'
import { TargetVariableEnum } from '@/types/Enum/TargetVariableEnum'
import Plan from '@/types/Plan'
import { AdditionalFieldTypes } from '@/types/Plan/AdditionalFieldTypes'
import enumOptionsToSelectOptionWithDescription from '@/utils/Descriptions/enumOptionsToSelectOptionWithDescription'
import { payoutFrequencyToDescription } from '@/utils/Descriptions/payoutFrequencyToDescription'
import { targetVariableToDescription } from '@/utils/Descriptions/targetVariableToDescription'
import notify from '@/utils/notify'
import { router, useForm } from '@inertiajs/vue3'
import CardOptions, { CardOptionsOption } from '../CardOptions.vue'

export interface AdditionalField {
    id: number
    type: AdditionalFieldTypes
    value: number
}

const possibleAdditionalFields: Array<AdditionalFieldTypes> = ['Kicker', 'Cliff', 'Cap', 'Dependency', 'Trigger']

const props = defineProps<{
    plan?: Plan
    agents: Array<Pick<Agent, 'id' | 'name'>>
    target_variable_options: Array<string>
    payout_frequency_options: Array<string>
}>()

const form = useForm({
    name: props.plan?.name || '',
    start_date: props.plan?.start_date || (null as Date | null), // first day of next month
    target_amount_per_month: props.plan?.target_amount_per_month || 0,
    target_variable: props.plan?.target_variable || ('' as TargetVariableEnum),
    payout_frequency: props.plan?.payout_frequency || ('' as PayoutFrequencyEnum),
    assigned_agent_ids: props.plan?.agents!.map((agent) => agent.id) || ([] as Array<number>),

    additionalFields: [] as Array<AdditionalField>,
})

function handleDateChange(newDate: Date) {
    form.start_date = newDate
}

function handleAgentSelect(id: number) {
    if (form.assigned_agent_ids.includes(id)) {
        form.assigned_agent_ids = form.assigned_agent_ids.filter((agentId) => agentId !== id)
    } else {
        form.assigned_agent_ids.push(id)
    }
}

function submit() {
    if (props.plan) {
        form.put(route('plans.update', props.plan.id), {
            onSuccess: () => notify('Plan updated', 'The new data will now be used for calculation.'),
        })
    } else {
        form.post(route('plans.store'), {
            onSuccess: () => notify('Plan stored', 'Your plan is now available for use.'),
        })
    }
}

function addAdditionalField(type: AdditionalFieldTypes): void {
    switch (type) {
        case 'Cap':
            form.additionalFields.push({
                type: type,
                id: form.additionalFields.length,
                value: 0,
            })
            break

        default:
            break
    }
}
</script>

<template>
    <div class="flex justify-center gap-20">
        <Card class="w-144">
            <h2 class="text-base font-semibold leading-7 text-gray-900">
                {{ props.plan ? 'Update Flatrate Commission Plan' : 'Create Flatrate Commission Plan' }}
            </h2>
            <p class="mt-1 text-sm leading-6 text-gray-600">
                Receive a fixed percentage of a certain variable such as ARR.
            </p>
            <form
                @submit.prevent="submit"
                class="divide-y divide-gray-200"
            >
                <div class="my-6 space-y-6">
                    <div>
                        <InputLabel
                            for="name"
                            value="Name"
                            required
                        />
                        <TextInput
                            type="text"
                            v-model="form.name"
                            name="name"
                            placeholder="SDR Commission Plan"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.name"
                        />
                    </div>
                    <div>
                        <InputLabel
                            for="start_date"
                            value="Start Date"
                            required
                        />
                        <DateInput
                            :date="form.start_date"
                            @date-changed="handleDateChange"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.start_date"
                        />
                    </div>
                    <div>
                        <InputLabel
                            for="target_amount_per_month"
                            value="Target Amount (per month)"
                            required
                        />
                        <CurrencyInput
                            :value="form.target_amount_per_month"
                            @set-value="(value: number) => (form.target_amount_per_month = value)"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.target_amount_per_month"
                        />
                    </div>
                    <div class="flex gap-5">
                        <div class="w-1/2">
                            <InputLabel
                                for="target_variable"
                                value="Target Variable"
                                required
                            />
                            <SelectWithDescription
                                :options="
                                    enumOptionsToSelectOptionWithDescription(
                                        props.target_variable_options,
                                        targetVariableToDescription
                                    )
                                "
                                @option-selected="(optionTitle: TargetVariableEnum) => form.target_variable = optionTitle"
                                :default="props.plan ? {
                                    title: form.target_variable,
                                    description: targetVariableToDescription[form.target_variable as TargetVariableEnum],
                                    current: true
                                } : undefined"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.target_variable"
                            />
                        </div>
                        <div class="w-1/2">
                            <InputLabel
                                for="payout_frequency"
                                value="Payout Frequency"
                                required
                            />
                            <SelectWithDescription
                                :options="
                                    enumOptionsToSelectOptionWithDescription(
                                        props.payout_frequency_options,
                                        payoutFrequencyToDescription
                                    )
                                "
                                @option-selected="(optionTitle: PayoutFrequencyEnum) => form.payout_frequency = optionTitle"
                                :default="props.plan ? {
                                    title: form.payout_frequency,
                                    description: payoutFrequencyToDescription[form.payout_frequency as PayoutFrequencyEnum],
                                    current: true
                                } : undefined"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.payout_frequency"
                            />
                        </div>
                    </div>

                    <div class="">
                        <InputLabel value="Need more complexity? Add one of the following.." />

                        <CardOptions
                            :options-per-row="5"
                            :options="
                                possibleAdditionalFields.map((type) => {
                                    return {
                                        title: type,
                                        disabled: form.additionalFields.map((field) => field.type).includes(type),
                                    }
                                })
                            "
                            @option-clicked="(option: CardOptionsOption<AdditionalFieldTypes>) => addAdditionalField(option.title)"
                        />
                    </div>

                    <AdditionalField
                        v-for="field in form.additionalFields"
                        :form="form"
                        :field="field"
                        :key="field.id"
                    />

                    <div>
                        <InputLabel
                            for="assigned_agent_ids"
                            value="Assigned Agents"
                        />

                        <MultiSelect
                            @agent-clicked="handleAgentSelect"
                            :options="props.agents"
                            :selected-ids="form.assigned_agent_ids"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.assigned_agent_ids"
                        />
                    </div>
                </div>

                <FormButtons
                    :positiveButtonText="props.plan ? 'Save' : 'Create'"
                    @cancel-button-clicked="router.get(route('plans.index'))"
                    @create-button-clicked="submit"
                />
            </form>
        </Card>
    </div>
</template>
