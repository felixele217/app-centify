<script setup lang="ts">
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue'
import Card from '@/Components/Card.vue'
import { highlightedText, subtleText } from '../styles'
import BarChart from './BarChart.vue'
import Plan from './Plan.vue'
</script>

<template>
    <Card>
        <div class="flex justify-between">
            <p :class="subtleText">Total Payout by Commission Plan</p>
            <PrimaryButton
                text="Details"
                class="bg-violet-900"
            />
        </div>
        <div class="my-5 flex justify-between">
            <p :class="highlightedText">10.748</p>
            <div class="flex gap-2">
                <Plan
                    color="bg-violet-950"
                    name="Plan A"
                />
                <Plan
                    color="bg-gray-950"
                    name="Plan B"
                />
                <Plan
                    color="bg-violet-700"
                    name="Plan C"
                />
            </div>
        </div>
        <BarChart />
    </Card>
</template>
