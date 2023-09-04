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
import { TriggerEnumCases } from '@/EnumCases/TriggerEnum'
import Agent from '@/types/Agent'
import { AdditionalPlanFieldEnum } from '@/types/Enum/AdditionalPlanFieldEnum'
import { KickerTypeEnum } from '@/types/Enum/KickerTypeEnum'
import { PlanCycleEnum } from '@/types/Enum/PlanCycleEnum'
import { SalaryTypeEnum } from '@/types/Enum/SalaryTypeEnum'
import { TargetVariableEnum } from '@/types/Enum/TargetVariableEnum'
import { TimeScopeEnum } from '@/types/Enum/TimeScopeEnum'
import { AssignedAgent, UpsertPlanForm } from '@/types/Form/UpsertPlanForm'
import Plan from '@/types/Plan/Plan'
import enumOptionsToSelectOptionWithDescription from '@/utils/Descriptions/enumOptionsToSelectOptionWithDescription'
import { planCycleToDescription } from '@/utils/Descriptions/planCycleToDescription'
import { targetVariableToDescription } from '@/utils/Descriptions/targetVariableToDescription'
import { triggerToDescription } from '@/utils/Descriptions/triggerToDescription'
import notify from '@/utils/notify'
import { router, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import UpsertAgentSlideOver from '../Agent/Index/UpsertAgentSlideOver.vue'
import CardOptions, { CardOptionsOption } from '../CardOptions.vue'
import PercentageInput from '../Form/PercentageInput.vue'
import SectionWithDescription from '../Form/SectionWithDescription.vue'
import InfoIcon from '../Icon/InfoIcon.vue'
import KickerForm from './KickerForm.vue'
import PlanDescription from './PlanDescription.vue'
import roundFloat from '@/utils/roundFloat'
import MonthInput from '../Form/MonthInput.vue'
import HideInProduction from '../System/HideInProduction.vue'

export interface AdditionalField {
    id: number
    type: AdditionalPlanFieldEnum
    value?: number
    text?: string
}

const props = defineProps<{
    plan?: Plan
    agents: Array<Pick<Agent, 'id' | 'name'>>
    target_variable_options: Array<TargetVariableEnum>
    payout_frequency_options: Array<PlanCycleEnum>
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
    plan_cycle: props.plan?.plan_cycle || ('' as PlanCycleEnum),
    assigned_agents:
        props.plan?.agents!.map((agent) => ({
            id: agent.id,
            share_of_variable_pay: agent.pivot.share_of_variable_pay * 100,
        })) || ([] as Array<AssignedAgent>),

    cliff: {
        threshold_in_percent: props.plan?.cliff?.threshold_in_percent
            ? roundFloat(props.plan?.cliff.threshold_in_percent * 100, 0)
            : null,
        time_scope: 'monthly' as TimeScopeEnum,
    },

    kicker: {
        type: props.plan?.kicker?.type || ('' as KickerTypeEnum),
        threshold_in_percent: props.plan?.kicker?.threshold_in_percent
            ? roundFloat(props.plan.kicker.threshold_in_percent * 100, 0)
            : null,
        payout_in_percent: props.plan?.kicker?.payout_in_percent
            ? roundFloat(props.plan.kicker.payout_in_percent * 100, 0)
            : null,
        salary_type: props.plan?.kicker?.salary_type || ('' as SalaryTypeEnum),
        time_scope: 'quarterly' as TimeScopeEnum,
    },

    cap: props.plan?.cap?.value || null,
    trigger: props.plan?.trigger || 'Demo scheduled',
})

const assignedAgentIds = computed(() => form.assigned_agents.map((assignedAgent) => assignedAgent.id))

function handleAgentSelect(id: number) {
    if (assignedAgentIds.value.includes(id)) {
        form.assigned_agents = form.assigned_agents.filter((assignedAgent) => assignedAgent.id !== id)
    } else {
        form.assigned_agents.push({
            id: id,
            share_of_variable_pay: 100,
        })
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
    option.title = option.title.replace('+ Add ', '') as AdditionalPlanFieldEnum

    const includesField = activeAdditionalFields.value.includes(option.title)

    if (includesField) {
        form.reset(option.title.toLowerCase() as 'kicker' | 'cliff' | 'cap')

        activeAdditionalFields.value = activeAdditionalFields.value.filter((field) => field !== option.title)
    } else {
        activeAdditionalFields.value = [...activeAdditionalFields.value, option.title]
    }
}

const startShareOfVariablePay = ref<number>(
    props.plan?.agents!.length ? props.plan.agents[0].pivot.share_of_variable_pay * 100 : 100
)

function handleUpdateShareOfVariablePay(newShareOfVariablePay: number): void {
    form.assigned_agents = form.assigned_agents.map((assignedAgent) => ({
        id: assignedAgent.id,
        share_of_variable_pay: newShareOfVariablePay,
    }))
}

function closeSlideOver(createdAgent: boolean) {
    if (createdAgent) {
        handleAgentSelect(props.agents[props.agents.length - 1].id)
    }

    isUpsertingAgent.value = false
}

const isUpsertingAgent = ref(false)
</script>

<template>
    <UpsertAgentSlideOver
        @close-upsert-agent-slide-over="closeSlideOver"
        :is-open="!!isUpsertingAgent"
        dusk="upsert-agent-slide-over-modal"
    />

    <div class="flex justify-center gap-20">
        <Card class="w-144">
            <h2 class="text-base font-semibold leading-7 text-gray-900">
                {{ props.plan ? 'Update Quota Attainment Plan' : 'Create Quota Attainment Plan' }}
            </h2>
            <p class="mt-1 text-sm leading-6 text-gray-600">
                Your agents receive a commission tied to their quota attainment.
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

                    <div class="flex gap-5">
                        <div class="w-1/2">
                            <InputLabel
                                for="plan_cycle"
                                value="Plan Cycle"
                                required
                            />

                            <SelectWithDescription
                                :options="
                                    enumOptionsToSelectOptionWithDescription(
                                        props.payout_frequency_options,
                                        planCycleToDescription
                                    )
                                "
                                v-model="form.plan_cycle"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.plan_cycle"
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

                            <HideInProduction>
                                <MonthInput
                                    :current-date="form.start_date"
                                    @date-changed="(newDate: Date) => (form.start_date = newDate)"
                                />
                            </HideInProduction>
                           
                            <InputError
                                class="mt-2"
                                :message="form.errors.start_date"
                            />
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center gap-1">
                            <InputLabel
                                for="assigned_agent_ids"
                                value="Assigned Agents"
                            />

                            <p class="text-xs font-medium text-gray-700">
                                (...missing an agent?
                                <span
                                    class="link"
                                    @click="isUpsertingAgent = true"
                                    >Create</span
                                >)
                            </p>
                        </div>

                        <MultiSelect
                            @option-clicked="handleAgentSelect"
                            :options="props.agents"
                            :selected-ids="assignedAgentIds"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.assigned_agents"
                        />
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
                                    hover-text="This variable will contribute to your agents commission."
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
                                v-model="form.target_variable"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.target_variable"
                            />
                        </div>
                        <div class="w-1/2">
                            <div class="flex items-center gap-1">
                                <InputLabel
                                    value="Target Trigger"
                                    required
                                />

                                <InfoIcon
                                    hover-text="Set a condition for your target variable."
                                    class="max-w-5 whitespace-pre-line text-gray-700"
                                />
                            </div>

                            <SelectWithDescription
                                :options="
                                    enumOptionsToSelectOptionWithDescription(
                                        usePage().props.environment === 'production'
                                            ? ['Demo scheduled']
                                            : TriggerEnumCases,
                                        triggerToDescription
                                    )
                                "
                                v-model="form.trigger"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.trigger"
                            />
                        </div>
                    </div>

                    <div class="flex gap-5">
                        <div class="w-1/2">
                            <div class="flex gap-1">
                                <InputLabel
                                    for="target_amount_per_month"
                                    value="Target Amount (per month)"
                                    required
                                />
                                <InfoIcon
                                    hover-text="Set a monthly goal for your agent."
                                    class="max-w-5 whitespace-pre-line text-gray-700"
                                />
                            </div>

                            <CurrencyInput v-model="form.target_amount_per_month" />

                            <InputError
                                class="mt-2"
                                :message="form.errors.target_amount_per_month"
                            />
                        </div>

                        <div class="w-1/2">
                            <div class="flex gap-1">
                                <InputLabel
                                    for="share_of_variable_pay"
                                    value="Share of Variable Pay"
                                    required
                                />

                                <InfoIcon
                                    hover-text="The percentage of the assigned agents' variable pay that is commissioned by this plan."
                                    class="max-w-5 whitespace-pre-line text-gray-700"
                                />
                            </div>
                            <div>
                                <PercentageInput
                                    v-model="startShareOfVariablePay"
                                    :maximum="100"
                                    @update:model-value="handleUpdateShareOfVariablePay"
                                />

                                <!-- <PrimaryButton
                                    type="button"
                                    class="mt-2 h-auto text-xs"
                                    padding="px-2 py-0 h-4"
                                    text="Customize"
                                /> -->
                            </div>

                            <InputError
                                class="mt-2"
                                :message="form.errors.assigned_agents"
                            />
                        </div>
                    </div>

                    <div>
                        <InputLabel value="Need more complexity? Add one of the following..." />

                        <CardOptions
                            :options-per-row="3"
                            :options="
                                AdditionalPlanFieldEnumCases.map((type) => ({
                                    title: '+ Add ' + type,
                                    selected: activeAdditionalFields.includes(type),
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
                        <SectionWithDescription
                            heading="Cliff"
                            description="Set a minimum threshold of quota attainment the agent has to reach to qualify for a commission."
                        >
                            <div>
                                <InputLabel
                                    value="Cliff"
                                    required
                                />
                                <PercentageInput
                                    v-model="form.cliff.threshold_in_percent"
                                    :maximum="100"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="(form.errors as Record<string, string>)['cliff.threshold_in_percent'] || (form.errors as Record<string, string>)['cliff.time_scope']"
                                />
                            </div>
                        </SectionWithDescription>
                    </div>

                    <div v-if="activeAdditionalFields.includes('Cap')">
                        <SectionWithDescription
                            heading="Cap"
                            description="Cap very high deals to a certain amount."
                        >
                            <div>
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
                        </SectionWithDescription>
                    </div>
                </div>

                <PlanDescription :form="form" />

                <FormButtons
                    :positiveButtonText="props.plan ? 'Save' : 'Create'"
                    @cancel-button-clicked="router.get(route('plans.index'))"
                    @create-button-clicked="submit"
                />
            </form>
        </Card>
    </div>
</template>
