<script setup lang="ts">
import { KickerTypeEnum } from '@/types/Enum/KickerTypeEnum'
import { SalaryTypeEnum } from '@/types/Enum/SalaryTypeEnum'
import InputError from '../Form/InputError.vue'
import InputLabel from '../Form/InputLabel.vue'
import PercentageInput from '../Form/PercentageInput.vue'
import Select from '../Form/Select.vue'

defineEmits<{
    'set-type': [type: KickerTypeEnum]
    'set-salary-type': [type: SalaryTypeEnum]
    'set-threshold-in-percent': [thresholdInPercent: number]
    'set-payout-in-percent': [payoutInPercent: number]
}>()

const props = defineProps<{
    kickerTypeOptions: Array<KickerTypeEnum>
    salaryTypeOptions: Array<SalaryTypeEnum>
    kicker: {
        type: KickerTypeEnum
        salary_type: SalaryTypeEnum
        threshold_in_percent: number
        payout_in_percent: number
    }
    errors: Record<string, string>
}>()
</script>

<template>
    <div class="space-y-4">
        <div class="grid grid-cols-3 gap-5">
            <div class="col-span-2">
                <InputLabel
                    value="Kicker Type"
                    required
                />

                <Select
                    :options="props.kickerTypeOptions"
                    :selected-option="props.kicker.type"
                    @option-selected="(option) => $emit('set-type', option)"
                />
            </div>
            <div>
                <InputLabel
                    value="Kicker Threshold"
                    required
                />

                <PercentageInput
                    :value="props.kicker.threshold_in_percent"
                    @set-value="(value) => $emit('set-threshold-in-percent', value)"
                />
            </div>
        </div>

        <div class="grid grid-cols-3 gap-5">
            <div class="col-span-2">
                <InputLabel
                    value="Salary Type"
                    required
                />

                <Select
                    :options="props.salaryTypeOptions"
                    :selected-option="props.kicker.salary_type"
                    @option-selected="(option) => $emit('set-salary-type', option)"
                />
            </div>
            <div>
                <InputLabel
                    value="Kicker Payout"
                    required
                />

                <PercentageInput
                    :value="props.kicker.payout_in_percent"
                    @set-value="(value) => $emit('set-payout-in-percent', value)"
                />
            </div>
        </div>
        <InputError
            :message="
                props.errors['kicker.type'] ||
                props.errors['kicker.payout_in_percent'] ||
                props.errors['kicker.threshold_in_percent'] ||
                props.errors['kicker.salary_type']
            "
        />
    </div>
</template>
