<script setup lang="ts">
import { PayoutFrequencyEnum } from '@/types/Enum/PayoutFrequencyEnum'
import { TargetVariableEnum } from '@/types/Enum/TargetVariableEnum'
import { InertiaForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import CurrencyInput from '../Form/CurrencyInput.vue'
import InputError from '../Form/InputError.vue'
import InputLabel from '../Form/InputLabel.vue'
import SelectWithDescription from '../Form/SelectWithDescription.vue'
import KickerForm from './KickerForm.vue'
import { AdditionalField } from './UpsertPlanCard.vue'
import PercentageInput from '../Form/PercentageInput.vue'

const props = defineProps<{
    field: AdditionalField
    form: InertiaForm<{
        name: string
        start_date: Date | null
        target_amount_per_month: number
        target_variable: TargetVariableEnum
        payout_frequency: PayoutFrequencyEnum
        assigned_agent_ids: Array<number>
        additionalFields: Array<AdditionalField>

            cliff_percentage: number
    }>
}>()

const value = ref(0)
const text = ref('')
</script>

<template>
    <div>
        <InputLabel
            v-if="props.field.type !== 'Kicker'"
            :value="props.field.type"
            required
        />

        <div v-if="props.field.type === 'Cap'">
            <CurrencyInput
                :value="value"
                @set-value="(newValue: number) => value = newValue"
            />

            <InputError />
        </div>




        <div v-if="props.field.type === 'Trigger'">
            <SelectWithDescription
                :options="[
                    {
                        title: 'Demo set',
                        description: 'The deal\'s demo_set_by field has a user assigned to it.',
                        current: true,
                    },
                ]"
                @option-selected="(optionTitle: 'Demo set') => text = optionTitle"
            />
        </div>

        <div v-if="props.field.type === 'Kicker'">
            <KickerForm />
        </div>

        <InputError />
    </div>
</template>
