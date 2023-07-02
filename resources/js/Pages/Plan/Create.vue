<script setup lang="ts">
import CurrencyInput from '@/Components/Form/CurrencyInput.vue'
import DateInput from '@/Components/Form/DateInput.vue'
import FormButtons from '@/Components/Form/FormButtons.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import MultiSelect from '@/Components/Form/MultiSelect.vue'
import SelectWithDescription from '@/Components/Form/SelectWithDescription.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import enumOptionsToSelectOptionWithDescription from '@/utils/Descriptions/enumOptionsToSelectOptionWithDescription'
import { payoutFrequencyToDescription } from '@/utils/Descriptions/payoutFrequencyToDescription'
import { targetVariableToDescription } from '@/utils/Descriptions/targetVariableToDescription'
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
    target_variable_options: Array<string>
    payout_frequency_options: Array<string>
}>()

const form = useForm({
    name: '',
    start_date: new Date(), // first day of next month
    target_amount_per_month: 0,
    target_variable: 0,
    payout_frequency: '',
    assigned_agents: [],
})

function handleDateChange(newDate: any) {
    form.start_date = newDate
}

function submit() {
    form.post(route('plans.store'))
}
</script>

<template>
    <div class="mx-20 rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-900/5">
        <h2 class="text-base font-semibold leading-7 text-gray-900">Create Flatrate Commission Plan</h2>
        <p class="mt-1 text-sm leading-6 text-gray-600">
            Receive a fixed percentage of a certain variable such as ARR.
        </p>

        <form @submit.prevent="submit">
            <div class="mt-6 w-1/2">
                <InputLabel
                    for="name"
                    value="Name"
                    required
                />

                <TextInput
                    type="text"
                    v-model="form.name"
                    name="name"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.name"
                />
            </div>

            <div class="mt-6 w-1/2">
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

            <div class="mt-6 w-1/2">
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

            <div class="mt-6 w-1/2">
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
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.target_variable"
                />
            </div>

            <div class="mt-6 w-1/2">
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
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.payout_frequency"
                />
            </div>

            <div class="mt-6 w-1/2">
                <InputLabel
                    for="assigned_agents"
                    value="Assigned Agents"
                />

                <MultiSelect :options="props.agents" />

                <InputError
                    class="mt-2"
                    :message="form.errors.assigned_agents"
                />
            </div>

            <div class="mt-5 border border-gray-100" />

            <FormButtons @create-button-clicked="submit" />
        </form>
    </div>
</template>
