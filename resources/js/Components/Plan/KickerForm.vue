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
        threshold_in_percent: number | null
        payout_in_percent: number | null
    }
    errors: Record<string, string>
}>()
</script>

<template>
    <div class="space-y-4 border-y border-gray-200 py-6">
        <div class="mb-5">
            <h4 class="font-semibold">Kicker</h4>
            <p class="mt-1 text-sm">Set an additional incentive for your agents achievements.</p>
        </div>

        <div class="grid grid-cols-3 gap-5">
            <div class="col-span-2">
                <InputLabel
                    value="Kicker Type"
                    required
                />

                <Select
                    :options="props.kickerTypeOptions"
                    :selected-option="props.kicker.type"
                    @option-selected="(kickerType: string) => $emit('set-type', kickerType)"
                />
            </div>
            <div>
                <InputLabel
                    value="Kicker Threshold"
                    required
                />

                <PercentageInput v-model="props.kicker.threshold_in_percent" />
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
                    @option-selected="(salaryType: string) => $emit('set-salary-type', salaryType)"
                />
            </div>
            <div>
                <InputLabel
                    value="Kicker Payout"
                    required
                />

                <PercentageInput v-model="props.kicker.payout_in_percent" />
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
