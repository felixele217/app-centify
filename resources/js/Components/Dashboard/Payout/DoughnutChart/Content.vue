<script setup lang="ts">
import sum from '@/utils/sum'
import tailwindToHex from '@/utils/tailwindToHex'
import { ArcElement, Chart as ChartJS, Tooltip } from 'chart.js'
import { Doughnut } from 'vue-chartjs'

const props = defineProps<{
    values: Array<number>
}>()

ChartJS.register(ArcElement, Tooltip)

const colors = [tailwindToHex['primary-500'], tailwindToHex['primary-300'], tailwindToHex['primary-100']].slice(
    0,
    props.values.length
)

const data = {
    datasets: [
        {
            backgroundColor: colors,
            data: [...props.values, sum(props.values) >= 100 ? 0 : 100 - sum(props.values)],
        },
    ],
}

const chartOptions = {
    responsive: true,
    cutout: '80%',
    plugins: {
        tooltip: {
            enabled: false,
        },
    },
}
</script>

<template>
    <div class="relative flex w-40 items-center justify-center">
        <Doughnut
            :data="data"
            :options="chartOptions"
        />

        <h2 class="absolute -mb-1">{{ sum(props.values).toFixed(0) }}%</h2>
    </div>
</template>
