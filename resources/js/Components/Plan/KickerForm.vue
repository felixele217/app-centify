<script setup lang="ts">
import { ref } from 'vue'
import InputLabel from '../Form/InputLabel.vue'
import PercentageInput from '../Form/PercentageInput.vue'
import SelectWithDescription from '../Form/SelectWithDescription.vue'

const kickerType = ref()
const kickerThreshold = ref(0)
const kickerPayout = ref(0)
const salaryType = ref()

const kickerTypeOptions = [
    { title: 'Salary based - one time', current: false },
    { title: 'Salary based - accelerator', current: false },
    { title: 'Fixed amount - one time', current: false },
    { title: 'Fixed amount - accelerator', current: false },
]

const salaryTypeOptions = [
    { title: 'Variable Salary (monthly)', current: false },
    { title: 'Base Salary (monthly)', current: false },
    { title: 'Full Salary (monthly)', current: false },
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

                <SelectWithDescription
                    :options="kickerTypeOptions"
                    @option-selected="(optionTitle: string) => kickerType = optionTitle"
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

                <SelectWithDescription
                    :options="salaryTypeOptions"
                    @option-selected="(optionTitle: string) => salaryType = optionTitle"
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
