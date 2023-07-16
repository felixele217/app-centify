<script setup lang="ts">
import { PayoutFrequencyEnum } from '@/types/Enum/PayoutFrequencyEnum'
import { TargetVariableEnum } from '@/types/Enum/TargetVariableEnum'
import { InertiaForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import CurrencyInput from '../Form/CurrencyInput.vue'
import InputError from '../Form/InputError.vue'
import InputLabel from '../Form/InputLabel.vue'
import { AdditionalField } from './UpsertPlanCard.vue'

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
    }>
}>()

const value = ref(0)
</script>

<template>
    <div>
        <InputLabel :value="props.field.type" />

        <div v-if="props.field.type === 'Cap'">
            <CurrencyInput
                :value="value"
                @set-value="(newValue: number) => value = newValue"
            />

            <InputError />
        </div>
    </div>
</template>
