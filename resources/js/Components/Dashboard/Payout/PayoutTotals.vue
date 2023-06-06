<script setup lang="ts">
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue'
import Card from '@/Components/Card.vue'
import { subtleText } from '../styles'
import BarChart from './BarChart/BarChart.vue'
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

        <BarChart />
    </Card>
</template>
