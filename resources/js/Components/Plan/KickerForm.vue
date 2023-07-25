<script setup lang="ts">
import { ref } from 'vue'
import InputLabel from '../Form/InputLabel.vue'
import PercentageInput from '../Form/PercentageInput.vue'
import Select from '../Form/Select.vue'

const kickerType = ref()
const kickerThreshold = ref(0)
const kickerPayout = ref(0)
const salaryType = ref()

const kickerTypeOptions = [
    { name: 'Salary based - one time' },
    { name: 'Salary based - accelerator' },
    { name: 'Fixed amount - one time' },
    { name: 'Fixed amount - accelerator' },
]

const salaryTypeOptions = [
    { name: 'Variable Salary (monthly)' },
    { name: 'Base Salary (monthly)' },
    { name: 'Full Salary (monthly)' },
]
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
                    :options="kickerTypeOptions"
                    :selected-option-name="kickerType"
                    @option-selected="(optionName: string) => kickerType = optionName"
                />
            </div>
            <div>
                <InputLabel
                    value="Kicker Threshold"
                    required
                />

                <PercentageInput
                    :value="kickerThreshold"
                    @set-value="(value: number) =>kickerThreshold = value"
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
                    :options="salaryTypeOptions"
                    :selected-option-name="kickerType"
                    @option-selected="(optionName: string) => salaryType = optionName"
                />
            </div>
            <div>
                <InputLabel
                    value="Kicker Payout"
                    required
                />

                <PercentageInput
                    :value="kickerPayout"
                    @set-value="(value: number) =>kickerPayout = value"
                />
            </div>
        </div>
    </div>
</template>
