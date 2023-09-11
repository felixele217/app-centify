<script setup lang="ts">
import euroDisplay from '@/utils/euroDisplay'
import tailwindToHex from '@/utils/tailwindToHex'
import { BarElement, CategoryScale, Chart as ChartJS, LinearScale, Tooltip } from 'chart.js'
import { computed } from 'vue'
import { Bar } from 'vue-chartjs'
import { KICKER_COMMISSION_COLOR, QUOTA_COMMISSION_COLOR } from './config'
import roundFloat from '@/utils/roundFloat'

const gc = 'bg-primary-300'

export type BarChartContentItem = {
    label: string
    quotaCommission: number
    kickerCommission: number
}

const props = defineProps<{
    items: Array<BarChartContentItem>
}>()

ChartJS.register(Tooltip, BarElement, CategoryScale, LinearScale)

const chartData = computed(() => ({
    labels: props.items.map((item) => item.label),
    datasets: [
        {
            data: props.items.map((item) => item.quotaCommission),
            label: 'Quota Commission',
            backgroundColor: tailwindToHex[QUOTA_COMMISSION_COLOR],
        },
        {
            data: props.items.map((item) => item.kickerCommission),
            label: 'Kicker Commission',
            backgroundColor: tailwindToHex[KICKER_COMMISSION_COLOR],
        },
    ],
}))

const max = Math.max(...props.items.map((item) => item.kickerCommission + item.quotaCommission))

const chartOptions = {
    barThickness: 40,
    borderRadius: 8,
    plugins: {
        tooltip: {
            callbacks: {
                label: function (context: any) {
                    return ` ${euroDisplay(context.raw)}`
                },
                title: function (context: any) {
                    return ` ${context[0].dataset.label}`
                },
            },
            displayColors: false,
        },
    },
    scales: {
        x: {
            stacked: true,
            grid: {
                display: false,
            },
        },
        y: {
            stacked: true,
            min: 0,
            max: max,
            ticks: {
                stepSize: max / 5,
                callback: function (value: number, index: number, values: Array<number>) {
                    const euroDigitCount = max.toString().length - 2

                    const roundedValue = roundFloat(value / 10 ** (euroDigitCount - 1), 0) * 10 ** (euroDigitCount - 1)

                    return euroDisplay(roundedValue)
                },
            },
        },
    },
    responsive: true,
}
</script>

<template>
    <div class="h-56">
        <Bar
            id="my-chart-id"
            :options="(chartOptions as any)"
            :data="chartData"
        />
    </div>
</template>
