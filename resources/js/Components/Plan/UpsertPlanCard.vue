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
import { AdditionalPlanFieldEnumCases } from '@/EnumCases/AdditionalPlanFieldEnum'
import Agent from '@/types/Agent'
import { AdditionalPlanFieldEnum } from '@/types/Enum/AdditionalPlanFieldEnum'
import { KickerTypeEnum } from '@/types/Enum/KickerTypeEnum'
import { PayoutFrequencyEnum } from '@/types/Enum/PayoutFrequencyEnum'
import { SalaryTypeEnum } from '@/types/Enum/SalaryTypeEnum'
import { TargetVariableEnum } from '@/types/Enum/TargetVariableEnum'
import { TimeScopeEnum } from '@/types/Enum/TimeScopeEnum'
import { UpsertPlanForm } from '@/types/Form/UpsertPlanForm'
import Plan from '@/types/Plan/Plan'
import { additionalPlanFieldToDescription } from '@/utils/Descriptions/additionalPlanFieldToDescription'
import enumOptionsToSelectOptionWithDescription from '@/utils/Descriptions/enumOptionsToSelectOptionWithDescription'
import { payoutFrequencyToDescription } from '@/utils/Descriptions/payoutFrequencyToDescription'
import { targetVariableToDescription } from '@/utils/Descriptions/targetVariableToDescription'
import { triggerToDescription } from '@/utils/Descriptions/triggerToDescription'
import notify from '@/utils/notify'
import { router, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import CardOptions, { CardOptionsOption } from '../CardOptions.vue'
import PercentageInput from '../Form/PercentageInput.vue'
import InfoIcon from '../Icon/InfoIcon.vue'
import KickerForm from './KickerForm.vue'
import PlanDescription from './PlanDescription.vue'

export interface AdditionalField {
    id: number
    type: AdditionalPlanFieldEnum
    value?: number
    text?: string
}

const props = defineProps<{
    plan?: Plan
    agents: Array<Agent>
    target_variable_options: Array<TargetVariableEnum>
    payout_frequency_options: Array<PayoutFrequencyEnum>
    kicker_type_options: Array<KickerTypeEnum>
    salary_type_options: Array<SalaryTypeEnum>
}>()

const activeAdditionalFields = ref<Array<AdditionalPlanFieldEnum>>(additionalFieldsOfPlan())

function additionalFieldsOfPlan() {
    const activeAdditionalFields = []

    if (props.plan?.cliff) {
        activeAdditionalFields.push('Cliff')
    }

    if (props.plan?.kicker) {
        activeAdditionalFields.push('Kicker')
    }

    if (props.plan?.cap) {
        activeAdditionalFields.push('Cap')
    }

    return activeAdditionalFields as Array<AdditionalPlanFieldEnum>
}

const form = useForm<UpsertPlanForm>({
    name: props.plan?.name || '',
    start_date: props.plan?.start_date ? new Date(props.plan.start_date) : null,
    target_amount_per_month: props.plan?.target_amount_per_month || null,
    target_variable: props.plan?.target_variable || ('' as TargetVariableEnum),
    payout_frequency: props.plan?.payout_frequency || ('' as PayoutFrequencyEnum),
    assigned_agent_ids: props.plan?.agents!.map((agent) => agent.id) || ([] as Array<number>),

    cliff: {
        threshold_in_percent: props.plan?.cliff?.threshold_in_percent
            ? props.plan?.cliff.threshold_in_percent * 100
            : null,
        time_scope: 'monthly' as TimeScopeEnum,
    },

    kicker: {
        type: props.plan?.kicker?.type || ('' as KickerTypeEnum),
        threshold_in_percent: props.plan?.kicker?.threshold_in_percent
            ? props.plan.kicker.threshold_in_percent * 100
            : null,
        payout_in_percent: props.plan?.kicker?.payout_in_percent ? props.plan.kicker.payout_in_percent * 100 : null,
        salary_type: props.plan?.kicker?.salary_type || ('' as SalaryTypeEnum),
        time_scope: 'quarterly' as TimeScopeEnum,
    },

    cap: props.plan?.cap?.value || null,
    trigger: 'demo_set_by',
})

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
            preserveScroll: true,
        })
    } else {
        form.post(route('plans.store'), {
            onSuccess: () => notify('Plan stored', 'Your plan is now available for use.'),
            preserveScroll: true,
        })
    }
}

function toggleAdditionalField(option: CardOptionsOption<AdditionalPlanFieldEnum>) {
    const includesField = activeAdditionalFields.value.includes(option.title)

    if (includesField) {
        form.reset(option.title.toLowerCase() as 'kicker' | 'cliff' | 'cap')

        activeAdditionalFields.value = activeAdditionalFields.value.filter((field) => field !== option.title)
    } else {
        activeAdditionalFields.value = [...activeAdditionalFields.value, option.title]
    }
}
</script>

<template>
    <div class="flex justify-center gap-20">
        <Card class="w-144">
            <h2 class="text-base font-semibold leading-7 text-gray-900">
                {{ props.plan ? 'Update Straight-Line Commission Plan' : 'Create Straight-Line Commission Plan' }}
            </h2>
            <p class="mt-1 text-sm leading-6 text-gray-600">
                Receive a fixed percentage of a certain variable such as ARR.
            </p>
            <form
                @submit.prevent="submit"
                class="divide-y divide-gray-200"
            >
                <div class="my-6 space-y-6">
                    <div class="flex gap-5">
                        <div class="w-1/2">
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

                        <div class="w-1/2">
                            <InputLabel
                                for="start_date"
                                value="Start Date"
                                required
                            />
                            <DateInput
                                :current-date="form.start_date"
                                @date-changed="(newDate: Date) => (form.start_date = newDate)"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.start_date"
                            />
                        </div>
                    </div>
                    <div class="flex gap-5">
                        <div class="w-1/2">
                            <div class="flex gap-1">
                                <InputLabel
                                    for="target_variable"
                                    value="Target Variable"
                                    required
                                />

                                <InfoIcon
                                    :hover-text="`The target variable links a plan to a specific field from your integration.`"
                                    class="max-w-5 whitespace-pre-line text-gray-700"
                                />
                            </div>

                            <SelectWithDescription
                                :options="
                                    enumOptionsToSelectOptionWithDescription(
                                        props.target_variable_options,
                                        targetVariableToDescription
                                    )
                                "
                                @option-selected="(optionTitle: string) => form.target_variable = (optionTitle as TargetVariableEnum)"
                                v-model="form.target_variable"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.target_variable"
                            />
                        </div>
                        <div class="w-1/2">
                            <div class="flex gap-1">
                                <InputLabel
                                    for="target_amount_per_month"
                                    value="Target Amount (per month)"
                                    required
                                />

                                <InfoIcon
                                    :hover-text="`Set a monthly target for the target variable.
                                                Example: If you have a quarterly ARR Target of 90k, you have to insert 30k here.`"
                                    class="max-w-5 whitespace-pre-line text-gray-700"
                                />
                            </div>
                            <CurrencyInput v-model="form.target_amount_per_month" />
                            <InputError
                                class="mt-2"
                                :message="form.errors.target_amount_per_month"
                            />
                        </div>
                    </div>

                    <div>
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
                            @option-selected="(optionTitle: string) => form.payout_frequency = (optionTitle as PayoutFrequencyEnum)"
                            v-model="form.payout_frequency"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.payout_frequency"
                        />
                    </div>

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

                    <div>
                        <div class="flex items-center gap-1">
                            <InputLabel
                                value="Trigger"
                                required
                            />

                            <InfoIcon
                                :hover-text="`Select the trigger event that this plan applies to.
                                Deals will only be taken into account if this condition is met.`"
                                class="max-w-5 whitespace-pre-line text-gray-700"
                            />
                        </div>

                        <SelectWithDescription
                            :options="enumOptionsToSelectOptionWithDescription(['demo_set_by'], triggerToDescription)"
                            v-model="form.trigger"
                            @option-selected="(optionTitle: string) => form.trigger = (optionTitle as 'demo_set_by')"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.trigger"
                        />
                    </div>

                    <div>
                        <InputLabel value="Need more complexity? Add one of the following..." />

                        <CardOptions
                            :options-per-row="3"
                            :options="
                                AdditionalPlanFieldEnumCases.map((type) => ({
                                    title: type,
                                    selected: activeAdditionalFields.includes(type),
                                    description: additionalPlanFieldToDescription[type],
                                }))
                            "
                            @option-clicked="(option: CardOptionsOption<string>) => toggleAdditionalField(option as CardOptionsOption<AdditionalPlanFieldEnum>)"
                        />
                    </div>

                    <div v-if="activeAdditionalFields.includes('Kicker')">
                        <KickerForm
                            :kicker="form.kicker"
                            :kicker-type-options="props.kicker_type_options"
                            :salary-type-options="props.salary_type_options"
                            @set-type="(type: KickerTypeEnum) => (form.kicker.type = type)"
                            @set-salary-type="(salaryType: SalaryTypeEnum) => (form.kicker.salary_type = salaryType)"
                            @set-threshold-in-percent="
                                (thresholdInPercent: number) => (form.kicker.threshold_in_percent = thresholdInPercent)
                            "
                            @set-payout-in-percent="
                                (payoutInPercent: number) => (form.kicker.payout_in_percent = payoutInPercent)
                            "
                            :errors="form.errors"
                        />
                    </div>

                    <div v-if="activeAdditionalFields.includes('Cliff')">
                        <InputLabel
                            value="Cliff"
                            required
                        />

                        <PercentageInput v-model="form.cliff.threshold_in_percent" />

                        <InputError
                            class="mt-2"
                            :message="(form.errors as Record<string, string>)['cliff.threshold_in_percent'] || (form.errors as Record<string, string>)['cliff.time_scope']"
                        />
                    </div>

                    <div v-if="activeAdditionalFields.includes('Cap')">
                        <InputLabel
                            value="Deal Cap"
                            required
                        />

                        <CurrencyInput v-model="form.cap" />

                        <InputError
                            class="mt-2"
                            :message="form.errors.cap"
                        />
                    </div>
                </div>

                <PlanDescription
                    :agents="props.agents"
                    :form="form"
                />

                <FormButtons
                    :positiveButtonText="props.plan ? 'Save' : 'Create'"
                    @cancel-button-clicked="router.get(route('plans.index'))"
                    @create-button-clicked="submit"
                />
            </form>
        </Card>
    </div>
</template>
